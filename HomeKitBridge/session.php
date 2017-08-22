<?php

include_once 'tlv.php';
include_once 'srp.php';

class HomeKitSession
{
    private $sendDebug = null;
    private $pairings = null;
    private $codes = null;
    private $manager = null;
    private $bridgeID = '';
    private $accessoryKP = '';

    //Flag for new sessions
    private $new = false;

    //Data handling
    private $data = '';

    //Data encryption
    private $encrypted = false;
    private $encryptedData = '';
    private $messageRecvKey = '';
    private $messageRecvCounter = 0;
    private $messageSendKey = '';
    private $messageSendCounter = 0;

    //Required for stage PS M1+M3
    private $salt = '';
    private $setupCode = '';
    private $privateValue = null;
    private $publicValue = null;

    //Required for stage PS M5
    private $sharedSecret = null;

    //Required for stage PV M1+M3
    private $sessionKey = null;

    private function SendDebug($message)
    {
        call_user_func($this->sendDebug, 'HomeKitSession', $message, 0);
    }

    public function __construct($sendDebug, $pairings, $codes, $manager, $bridgeID, $accessoryKP, $sessionData)
    {
        $this->sendDebug = $sendDebug;
        $this->pairings = $pairings;
        $this->codes = $codes;
        $this->manager = $manager;
        $this->bridgeID = $bridgeID;
        $this->accessoryKP = $accessoryKP;

        //Fresh session use the predefined values
        if ($sessionData == '') {
            $this->new = true;
            return;
        }

        //Decode session data which is JSON encoded
        $json = json_decode($sessionData);

        //Copy data
        $this->data = $json->data;

        //Copy encryption
        $this->encrypted = $json->encrypted;
        $this->encryptedData = hex2bin($json->encryptedData);
        $this->messageRecvKey = hex2bin($json->messageRecvKey);
        $this->messageRecvCounter = $json->messageRecvCounter;
        $this->messageSendKey = hex2bin($json->messageSendKey);
        $this->messageSendCounter = $json->messageSendCounter;

        //Required for stage PS M1+M3
        $this->setupCode = $json->setupCode;
        $this->salt = hex2bin($json->salt);
        $this->privateValue = hex2bin($json->privateValue);
        $this->publicValue = hex2bin($json->publicValue);

        //Required for stage PS M5 and PV M1+M3
        $this->sharedSecret = hex2bin($json->sharedSecret);

        //Required for stage PV M1+M3
        $this->sessionKey = hex2bin($json->sessionKey);
    }

    public function __toString()
    {
        return json_encode([
            'data'               => $this->data,
            'encrypted'          => $this->encrypted,
            'encryptedData'      => bin2hex($this->encryptedData),
            'messageRecvKey'     => bin2hex($this->messageRecvKey),
            'messageRecvCounter' => $this->messageRecvCounter,
            'messageSendKey'     => bin2hex($this->messageSendKey),
            'messageSendCounter' => $this->messageSendCounter,
            'setupCode'          => $this->setupCode,
            'salt'               => bin2hex($this->salt),
            'privateValue'       => bin2hex($this->privateValue),
            'publicValue'        => bin2hex($this->publicValue),
            'sharedSecret'       => bin2hex($this->sharedSecret),
            'sessionKey'         => bin2hex($this->sessionKey),
        ]);
    }

    public function processData($data)
    {

        //Make check for disappeared sessions
        if ($this->new) {

            //If we receive (first) encrypted data on a new session, then there is something wrong.
            if($this->data == '' && substr($data, 0, 4) !== 'POST') {
                $this->sendDebug('Session data lost. We cannot resume this connection!');

                return;
            }
        }

        //If the session is in an encrypted state we need to decrypt first
        if ($this->encrypted) {
            //Append data
            $this->encryptedData .= $data;

            if (strlen($this->encryptedData) < 2) {
                $this->sendDebug('Waiting for data...');

                return;
            }

            $expectedLength = unpack('v', $this->encryptedData)[1];
            if (strlen($this->encryptedData) < $expectedLength + 2 /* Length */ + 16 /* AuthTag */) {
                $this->sendDebug('Waiting for data... ' . strlen($this->encryptedData) . ' / ' . $expectedLength);

                return;
            }

            $message = substr($this->encryptedData, 2, $expectedLength + 16);
            $ad = substr($this->encryptedData, 0, 2);

            if (PHP_INT_SIZE == 4) {
                $nonce = "\0\0\0\0" . pack('V', $this->messageRecvCounter) . "\0\0\0\0";
            } else {
                $nonce = "\0\0\0\0" . pack('P', $this->messageRecvCounter);
            }

            $decryptedData = sodium_crypto_aead_chacha20poly1305_ietf_decrypt($message, $ad, $nonce, $this->messageRecvKey);
            if ($decryptedData == null) {
                $this->sendDebug('Decrypting failed!');

                //FIXME: We need to invalidate the whole session!

                return;
            }

            //Increment nonce counter
            $this->messageRecvCounter++;

            //We consumed the data
            $this->encryptedData = '';

            //Append decrypted data
            $this->data .= $decryptedData;
        } else {
            //Append data
            $this->data .= $data;
        }

        if ($this->encrypted) {
            $this->SendDebug('Decrypted data: ' . $this->data);
        } else {
            $this->SendDebug('Plain data: ' . $this->data);
        }

        //Check if we can parse the complete packet.
        //Otherwise bail out and wait for more data
        if (!($http = $this->parseHTTP($this->data))) {
            $this->SendDebug('Incomplete HTTP packet');

            return;
        }

        //We consumed the data
        $this->data = '';

        //Process different methods and uris
        switch ($http['method']) {
            case 'GET':
                switch ($http['uri']) {
                    case '/accessories':
                        return $this->getAccessories($http);
                    case '/characteristics':
                        return $this->getCharacteristics($http);
                    default:
                        $this->SendDebug('Unsupported uri for GET: ' . $http['uri']);

                        return;
                }
                break;
            case 'PUT':
                switch ($http['uri']) {
                    case '/characteristics':
                        return $this->putCharacteristics($http);
                    default:
                        $this->SendDebug('Unsupported uri for PUT' . $http['uri']);

                        return;
                }
                break;
            case 'POST':
                switch ($http['uri']) {
                    case '/pair-setup':
                        return $this->postPairSetup($http);
                    case '/pair-verify':
                        return $this->postPairVerify($http);
                    case '/pairings':
                        return $this->postPairings($http);
                    case '/identify':
                        return $this->postIdentify($http);
                    default:
                        $this->SendDebug('Unsupported uri for POST' . $http['uri']);

                        return;
                }
            default:
                $this->SendDebug('Unsupported method ' . $http['method']);

                return;
        }
    }

    private function parseHTTP($data)
    {

        //Check for complete header
        $headerEnd = strpos($data, "\r\n\r\n");
        if ($headerEnd === false) {
            $this->SendDebug('Header is incomplete');

            return;
        }

        $rawHeaders = explode("\r\n", substr($data, 0, $headerEnd));

        //Check for at least one array item
        if (count($rawHeaders) == 0) {
            $this->SendDebug('Header is empty');

            return;
        }

        $top = explode(' ', $rawHeaders[0]);

        //Check if the first header line matches requirements
        if (count($top) != 3) {
            $this->SendDebug('Invalid first header line');

            return;
        }

        $method = $top[0];
        $uri = $top[1];
        $version = $top[2];

        //Parse uri
        $query = [];
        if (strpos($uri, '?') !== false) {
            $params = substr($uri, strpos($uri, '?') + 1);
            $uri = substr($uri, 0, strpos($uri, '?'));
            parse_str($params, $query);
        }

        //Remove first item
        unset($rawHeaders[0]);

        //Process headers
        $headers = [];
        foreach ($rawHeaders as $headerLine) {
            $header = explode(':', $headerLine, 2);
            $headers[strtolower(trim($header[0]))] = trim($header[1]);
        }

        //We do not require advanced data handling for GET requests
        if ($method == 'GET') {
            $body = null;
        } else {
            //Content-Length is required
            if (!isset($headers['content-length'])) {
                $this->SendDebug('Content-Length missing');

                return;
            }

            //Check if the body is complete
            $body = substr($data, $headerEnd + 4);
            if (strlen($body) != intval($headers['content-length'])) {
                $this->SendDebug('Content length does not match. Actual: ' . strlen($body) . ', Expected: ' . intval($headers['content-length']));

                return;
            }
        }

        //Return the packet so we can process it
        return [
            'method'  => $method,
            'query'   => $query,
            'version' => $version,
            'uri'     => $uri,
            'headers' => $headers,
            'body'    => $body
        ];
    }

    private function buildHTTP($http)
    {
        $content = $http['version'] . ' ' . $http['status'] . "\r\n";

        if ($http['headers']) {
            foreach ($http['headers'] as $key => $value) {
                $content .= $key . ': ' . $value . "\r\n";
            }
        }

        if ($http['body']) {
            if (!isset($http['Content-Length'])) {
                $content .= 'Content-Length: ' . strlen($http['body']) . "\r\n";
            }
        }

        $content .= "\r\n";

        if ($http['body']) {
            $content .= $http['body'];
        }

        return $content;
    }

    private function buildPairingResponse($body)
    {

        //A fatal error occoured. Forward null
        if ($body == null) {
            return;
        }

        //FIXME: This is not very performant
        if ((new TLVParser($body))->getByType(TLVType::Error)) {
            $status = '400 Bad Request';
        } else {
            $status = '200 OK';
        }

        return $this->buildHTTP([
            'status'  => $status,
            'version' => 'HTTP/1.1',
            'headers' => [
                'Content-Type' => 'application/pairing+tlv8'
            ],
            'body' => $body
        ]);
    }

    private function postPairSetup($http)
    {
        $tlvs = new TLVParser($http['body']);

        $tlvState = $tlvs->getByType(TLVType::State);

        //State is required
        if (!$tlvState) {
            $this->SendDebug('State missing');

            return;
        }

        switch ($tlvState->getState()) {
            case TLVState::M1:
                return $this->buildPairingResponse($this->handlePairSetupM1($tlvs));
            case TLVState::M3:
                return $this->buildPairingResponse($this->handlePairSetupM3($tlvs));
            case TLVState::M5:
                return $this->buildPairingResponse($this->handlePairSetupM5($tlvs));
            default:
                $this->SendDebug('Unsupported pair setup state ' . $tlvState->getState());

                return;
        }
    }

    private function handlePairSetupM1($tlvs)
    {
        $response = '';

        $tlvMethod = $tlvs->getByType(TLVType::Method);
        if (!$tlvMethod || $tlvMethod->getMethod() != TLVMethod::PairSetup) {
            $this->SendDebug('Method missing or not PairSetup');
            $response .= TLVBuilder::State(TLVState::M2);
            $response .= TLVBuilder::Error(TLVError::Unknown);

            return $response;
        }

        if (false /* Check for retry count */) {
            $this->SendDebug('RetryCount exceeded while PairSetup');
            $response .= TLVBuilder::State(TLVState::M2);
            $response .= TLVBuilder::Error(TLVError::MaxTries);

            return $response;
        }

        //Fetch current setup code and salt for pairing process
        $this->setupCode = $this->codes->getSetupCode();

        //Abort if there is not valid setup code
        if (!$this->setupCode) {
            $this->SendDebug('No active SetupCode was found. Aborting.');

            return;
        }

        $this->salt = random_bytes(16);

        //Generate our private value b
        $this->privateValue = random_bytes(16);

        //Create the SRP context
        $srp = new SRP6aServer($this->salt, 'Pair-Setup', $this->setupCode, $this->privateValue);

        //Generate our public value B
        $this->publicValue = $srp->createPublicValue();

        //Build TLV response
        $response .= TLVBuilder::State(TLVState::M2);
        $response .= TLVBuilder::PublicKey($this->publicValue);
        $response .= TLVBuilder::Salt($this->salt);

        return $response;
    }

    private function handlePairSetupM3($tlvs)
    {
        $response = '';

        $tlvError = $tlvs->getByType(TLVType::Error);
        if ($tlvError) {
            $this->SendDebug('Error while PairSetup M3: ' . $tlvError->getError());

            return;
        }

        $tlvPublicKey = $tlvs->getByType(TLVType::PublicKey);
        $tlvProof = $tlvs->getByType(TLVType::Proof);
        if (!$tlvPublicKey || !$tlvProof) {
            $this->SendDebug('PublicKey or Proof missing');
            $response .= TLVBuilder::State(TLVState::M4);
            $response .= TLVBuilder::Error(TLVError::Unknown);

            return $response;
        }

        //Recover the SRP context
        $srp = new SRP6aServer($this->salt, 'Pair-Setup', $this->setupCode, $this->privateValue);

        //Create premaster secret
        $S = $srp->createPresharedSecret($tlvPublicKey->getPublicKey(), $this->publicValue);

        //Create session key
        $K = $srp->createSessionKey($S);

        //Verify proof
        if ($srp->verifyProof($tlvPublicKey->getPublicKey(), $this->publicValue, $K, $tlvProof->getProof())) {

            //Store session key
            $this->sharedSecret = $K;

            //Respond with our proof
            $response .= TLVBuilder::State(TLVState::M4);
            $response .= TLVBuilder::Proof($srp->createProof($tlvPublicKey->getPublicKey(), $tlvProof->getProof(), $K));

            return $response;
        } else {
            $this->SendDebug('Proof is invalid');
            $response .= TLVBuilder::State(TLVState::M4);
            $response .= TLVBuilder::Error(TLVError::Authentication);

            return $response;
        }
    }

    private function handlePairSetupM5($tlvs)
    {
        $response = '';

        $tlvEncryptedData = $tlvs->getByType(TLVType::EncryptedData);

        $sessionKey = hash_hkdf('sha512', $this->sharedSecret, 32, 'Pair-Setup-Encrypt-Info', 'Pair-Setup-Encrypt-Salt');

        $decryptedData = sodium_crypto_aead_chacha20poly1305_ietf_decrypt($tlvEncryptedData->getEncryptedData(), '', "\0\0\0\0PS-Msg05", $sessionKey);

        //Verify that data is authentic
        if ($decryptedData == null) {
            $response .= TLVBuilder::State(TLVState::M6);
            $response .= TLVBuilder::Error(TLVError::Authentication);

            return $response;
        }

        $tlvs = new TLVParser($decryptedData);

        $tlvError = $tlvs->getByType(TLVType::Error);
        if ($tlvError) {
            $this->SendDebug('Error while PairSetup M5: ' . $tlvError->getError());

            return;
        }

        $tlvIdentifier = $tlvs->getByType(TLVType::Identifier);
        $tlvPublicKey = $tlvs->getByType(TLVType::PublicKey);
        $tlvSignature = $tlvs->getByType(TLVType::Signature);
        if (!$tlvIdentifier || !$tlvPublicKey || !$tlvSignature) {
            $this->SendDebug('Identifier, PublicKey or Signature missing');
            $response .= TLVBuilder::State(TLVState::M6);
            $response .= TLVBuilder::Error(TLVError::Unknown);

            return $response;
        }

        //Verify signature of controllers info
        $iOSDeviceX = hash_hkdf('sha512', $this->sharedSecret, 32, 'Pair-Setup-Controller-Sign-Info', 'Pair-Setup-Controller-Sign-Salt');
        $iOSDevicePairingID = $tlvIdentifier->getIdentifier();
        $iOSDeviceLTPK = $tlvPublicKey->getPublicKey();
        $iOSDeviceInfo = $iOSDeviceX . $iOSDevicePairingID . $iOSDeviceLTPK;

        if (!sodium_crypto_sign_verify_detached($tlvSignature->getSignature(), $iOSDeviceInfo, $tlvPublicKey->getPublicKey())) {
            $this->SendDebug('Signature verify failed');
            $response .= TLVBuilder::State(TLVState::M6);
            $response .= TLVBuilder::Error(TLVError::Authentication);

            return $response;
        }

        //Fetch Ed25519 keys
        $AccessoryLTPK = sodium_crypto_sign_publickey($this->accessoryKP);
        $AccessoryLTSK = sodium_crypto_sign_secretkey($this->accessoryKP);

        //Generate signature of accessory info
        $AccessoryX = hash_hkdf('sha512', $this->sharedSecret, 32, 'Pair-Setup-Accessory-Sign-Info', 'Pair-Setup-Accessory-Sign-Salt');
        $AccessoryPairingID = $this->bridgeID;
        //$AccessoryLTPK <- see above
        $AccessoryInfo = $AccessoryX . $AccessoryPairingID . $AccessoryLTPK;

        //Build signature
        $AccessorySignature = sodium_crypto_sign_detached($AccessoryInfo, $AccessoryLTSK);

        //Build subTLVs
        $responsePayload = ''; //This will be encrypted
        $responsePayload .= TLVBuilder::Identifier($AccessoryPairingID);
        $responsePayload .= TLVBuilder::PublicKey($AccessoryLTPK);
        $responsePayload .= TLVBuilder::Signature($AccessorySignature);

        //Encrypt payload
        $responsePayload = sodium_crypto_aead_chacha20poly1305_ietf_encrypt($responsePayload, '', "\0\0\0\0PS-Msg06", $sessionKey);

        //Build TLVs
        $response .= TLVBuilder::State(TLVState::M6);
        $response .= TLVBuilder::EncryptedData($responsePayload);

        //Save long term public key for identifier
        $this->pairings->addPairing($tlvIdentifier->getIdentifier(), $tlvPublicKey->getPublicKey(), TLVPermissions::Admin);

        //Cleanup sensitive session data
        $this->salt = '';
        $this->setupCode = '';
        $this->data = '';
        $this->encrypted = false;
        $this->privateValue = null;
        $this->publicValue = null;
        $this->sharedSecret = null;

        //Remove current setup code
        $this->codes->removeSetupCode();

        return $response;
    }

    private function postPairVerify($http)
    {
        $tlvs = new TLVParser($http['body']);

        $tlvState = $tlvs->getByType(TLVType::State);

        //State is required
        if (!$tlvState) {
            $this->SendDebug('State missing');

            return;
        }

        switch ($tlvState->getState()) {
            case TLVState::M1:
                return $this->buildPairingResponse($this->handlePairVerifyM1($tlvs));
            case TLVState::M3:
                return $this->buildPairingResponse($this->handlePairVerifyM3($tlvs));
            default:
                $this->SendDebug('Unsupported pair verify state ' . $tlvState->getState());

                return;
        }
    }

    private function handlePairVerifyM1($tlvs)
    {
        $response = '';

        $tlvPublicKey = $tlvs->getByType(TLVType::PublicKey);
        if (!$tlvPublicKey) {
            $this->SendDebug('PublicKey missing');
            $response .= TLVBuilder::State(TLVState::M2);
            $response .= TLVBuilder::Error(TLVError::Unknown);

            return $response;
        }

        //Generate Curve25519 key pair
        $keyPair = sodium_crypto_box_keypair();
        $keyPairSK = sodium_crypto_box_secretkey($keyPair);
        $keyPairPK = sodium_crypto_box_publickey($keyPair);

        //Generate shared secret
        $this->sharedSecret = sodium_crypto_scalarmult($keyPairSK, $tlvPublicKey->getPublicKey());

        //Generate AccessoryInfo
        $AccessoryPK = $keyPairPK;
        $AccessoryPairingID = $this->bridgeID;
        $iOSDevicePK = $tlvPublicKey->getPublicKey();
        $AccessoryInfo = $AccessoryPK . $AccessoryPairingID . $iOSDevicePK;

        //Fetch Ed25519 keys
        $AccessoryLTSK = sodium_crypto_sign_secretkey($this->accessoryKP);

        //Build signature
        $AccessorySignature = sodium_crypto_sign_detached($AccessoryInfo, $AccessoryLTSK);

        //Build subTLVs
        $responsePayload = ''; //This will be encrypted
        $responsePayload .= TLVBuilder::Identifier($AccessoryPairingID);
        $responsePayload .= TLVBuilder::Signature($AccessorySignature);

        //Derive SessionKey
        $this->sessionKey = hash_hkdf('sha512', $this->sharedSecret, 32, 'Pair-Verify-Encrypt-Info', 'Pair-Verify-Encrypt-Salt');

        //Save our SK and iOSDevice's SK
        $this->privateValue = $AccessoryPK;
        $this->publicValue = $iOSDevicePK;

        //Encrypt payload
        $responsePayload = sodium_crypto_aead_chacha20poly1305_ietf_encrypt($responsePayload, '', "\0\0\0\0PV-Msg02", $this->sessionKey);

        //Build TLVs
        $response .= TLVBuilder::State(TLVState::M2);
        $response .= TLVBuilder::PublicKey($AccessoryPK);
        $response .= TLVBuilder::EncryptedData($responsePayload);

        return $response;
    }

    private function handlePairVerifyM3($tlvs)
    {
        $response = '';

        $tlvEncryptedData = $tlvs->getByType(TLVType::EncryptedData);

        $decryptedData = sodium_crypto_aead_chacha20poly1305_ietf_decrypt($tlvEncryptedData->getEncryptedData(), '', "\0\0\0\0PV-Msg03", $this->sessionKey);

        //Verify that data is authentic
        if ($decryptedData == null) {
            $this->SendDebug('Decrypting failed');
            $response .= TLVBuilder::State(TLVState::M4);
            $response .= TLVBuilder::Error(TLVError::Authentication);

            return $response;
        }

        $tlvs = new TLVParser($decryptedData);

        //Search if we know this PairingID
        $tlvIdentifier = $tlvs->getByType(TLVType::Identifier);

        $iOSDeviceLTPK = $this->pairings->getPairingPublicKey($tlvIdentifier->getIdentifier());
        if ($iOSDeviceLTPK == null) {
            $this->SendDebug('Identifier is invalid');
            $response .= TLVBuilder::State(TLVState::M4);
            $response .= TLVBuilder::Error(TLVError::Authentication);

            return $response;
        }

        //Get Signature
        $tlvSignature = $tlvs->getByType(TLVType::Signature);

        //Build DeviceInfo
        $iOSDeviceInfo = $this->publicValue . $tlvIdentifier->getIdentifier() . $this->privateValue;

        //Verify
        if (!sodium_crypto_sign_verify_detached($tlvSignature->getSignature(), $iOSDeviceInfo, $iOSDeviceLTPK)) {
            $this->SendDebug('Signature verify failed');
            $response .= TLVBuilder::State(TLVState::M4);
            $response .= TLVBuilder::Error(TLVError::Authentication);

            return $response;
        }

        //Build TLVs
        $response .= TLVBuilder::State(TLVState::M4);

        //Generate Receive and Send key for this session
        $this->messageRecvKey = hash_hkdf('sha512', $this->sharedSecret, 32, 'Control-Write-Encryption-Key', 'Control-Salt');
        $this->messageSendKey = hash_hkdf('sha512', $this->sharedSecret, 32, 'Control-Read-Encryption-Key', 'Control-Salt');

        //Cleanup sensitive data
        $this->sessionKey = null;
        $this->sharedSecret = null;
        $this->privateValue = null;
        $this->publicValue = null;

        //Mark Session as encrypted
        $this->encrypted = true;
        $this->sendDebug('Session is now using encrypted communication!');

        return $response;
    }

    private function buildEncryptedResponse($body)
    {

        //Print debug
        $this->sendDebug('Encrypted data: ' . $body);

        //A fatal error occoured. Forward null
        if ($body == null) {
            return;
        }

        //Split into packets 1024 bytes each
        $parts = str_split($body, 1024);

        //Clear body
        $body = '';

        //Encrypt each part
        foreach ($parts as $part) {
            $length = pack('v', strlen($part));

            if (PHP_INT_SIZE == 4) {
                $nonce = "\0\0\0\0" . pack('V', $this->messageSendCounter) . "\0\0\0\0";
            } else {
                $nonce = "\0\0\0\0" . pack('P', $this->messageSendCounter);
            }

            //Encrypt and seal the response body
            $encryptedBody = sodium_crypto_aead_chacha20poly1305_ietf_encrypt($part, $length, $nonce, $this->messageSendKey);

            //Add decrypted body length in front
            $body .= $length . $encryptedBody;

            //Increment message counter
            $this->messageSendCounter++;
        }

        //Build response
        return $body;
    }

    private function postPairings($http)
    {
        $tlvs = new TLVParser($http['body']);

        $tlvMethod = $tlvs->getByType(TLVType::Method);
        if (!$tlvMethod) {
            $this->SendDebug('Method missing');

            return;
        }

        $tlvState = $tlvs->getByType(TLVType::State);
        if (!$tlvState) {
            $this->SendDebug('State missing');

            return;
        }

        if ($tlvState->getState() != TLVState::M1) {
            $this->SendDebug('State is not M1');

            return;
        }

        switch ($tlvMethod->getMethod()) {
            case TLVMethod::AddPairing:
                return $this->buildEncryptedResponse($this->buildPairingResponse($this->handleAddPairing($tlvs)));
            case TLVMethod::RemovePairing:
                return $this->buildEncryptedResponse($this->buildPairingResponse($this->handleRemovePairing($tlvs)));
            case TLVMethod::ListPairings:
                return $this->buildEncryptedResponse($this->buildPairingResponse($this->handleListPairing($tlvs)));
            default:
                $this->SendDebug('Unsupported pairing method ' . $tlvMethod->getMethod());

                return;
        }
    }

    private function handleAddPairing($tlvs)
    {
        $response = '';

        if (!$this->encrypted) {
            $response .= TLVBuilder::State(TLVState::M2);
            $response .= TLVBuilder::Error(TLVError::Authentication);

            return $response;
        }

        $tlvIdentifier = $tlvs->getByType(TLVType::Identifier);
        if (!$tlvIdentifier) {
            $this->SendDebug('Identifier is missing');
            $response .= TLVBuilder::State(TLVState::M2);
            $response .= TLVBuilder::Error(TLVError::Unknown);

            return $response;
        }

        $tlvPublicKey = $tlvs->getByType(TLVType::PublicKey);
        if (!$tlvPublicKey) {
            $this->SendDebug('PublicKey missing');
            $response .= TLVBuilder::State(TLVState::M2);
            $response .= TLVBuilder::Error(TLVError::Unknown);

            return $response;
        }

        $tlvPermissions = $tlvs->getByType(TLVType::Permissions);
        if (!$tlvPermissions) {
            $this->SendDebug('Permissions is missing');
            $response .= TLVBuilder::State(TLVState::M2);
            $response .= TLVBuilder::Error(TLVError::Unknown);

            return $response;
        }

        $this->pairings->addPairing($tlvIdentifier->getIdentifier(), $tlvPublicKey->getPublicKey(), $tlvPermissions->getPermissions());

        $response .= TLVBuilder::State(TLVState::M2);

        return $response;
    }

    private function handleRemovePairing($tlvs)
    {
        $response = '';

        if (!$this->encrypted) {
            $response .= TLVBuilder::State(TLVState::M2);
            $response .= TLVBuilder::Error(TLVError::Authentication);

            return $response;
        }

        $tlvIdentifier = $tlvs->getByType(TLVType::Identifier);
        if (!$tlvIdentifier) {
            $this->SendDebug('Identifier is missing');
            $response .= TLVBuilder::State(TLVState::M2);
            $response .= TLVBuilder::Error(TLVError::Unknown);

            return $response;
        }

        $this->pairings->removePairing($tlvIdentifier->getIdentifier());

        $response .= TLVBuilder::State(TLVState::M2);

        return $response;
    }

    private function handleListPairing($tlvs)
    {
        $response = '';

        if (!$this->encrypted) {
            $response .= TLVBuilder::State(TLVState::M2);
            $response .= TLVBuilder::Error(TLVError::Authentication);

            return $response;
        }

        $pairings = $this->pairings->listPairings();

        $response .= TLVBuilder::State(TLVState::M2);

        $first = true;
        foreach ($pairings as $identifier => $publicKey) {
            if (!$first) {
                $response .= TLVBuilder::Seperator();
            } else {
                $first = false;
            }
            $response .= TLVBuilder::Identifier($identifier);
            $response .= TLVBuilder::PublicKey($publicKey);
            $response .= TLVBuilder::Permissions(TLVPermissions::Admin);
        }

        return $response;
    }

    private function postIdentify($http)
    {
        return $this->buildHTTP([
            'status'  => '204 No Content',
            'version' => 'HTTP/1.1',
            'headers' => null,
            'body'    => null
        ]);
    }

    private function putCharacteristics($http)
    {
        $data = json_decode($http['body'], true);
        if (isset($data['characteristics'])) {
            foreach ($data['characteristics'] as $characteristic) {
                if (isset($characteristic['value'])) {
                    $this->manager->putCharacteristics($characteristic['aid'], $characteristic['iid'], $characteristic['value']);
                } else {
                    $this->sendDebug('Unsupported put characteristic: ' . print_r($characteristic, true));
                }
            }
        }

        return $this->buildEncryptedResponse($this->buildHTTP([
            'status'  => '204 No Content',
            'version' => 'HTTP/1.1',
            'headers' => null,
            'body'    => null
        ]));
    }

    private function getCharacteristics($http)
    {
        $characteristics = [];

        $ids = explode(',', $http['query']['id']);

        foreach ($ids as $id) {
            $target = explode('.', $id);
            $aid = intval($target[0]);
            $iid = intval($target[1]);
            $value = $this->manager->getCharacteristics($aid, $iid);
            $characteristics[] = [
                'aid'   => $aid,
                'iid'   => $iid,
                'value' => $value
            ];
        }

        return $this->buildEncryptedResponse($this->buildHTTP([
            'status'  => '200 OK',
            'version' => 'HTTP/1.1',
            'headers' => [
                'Content-Type' => 'application/hap+json'
            ],
            'body' => json_encode([
                'characteristics' => $characteristics
            ])
        ]));
    }

    private function buildAccessoriesResponse($body)
    {

        //Build the http response
        return $this->buildHTTP([
            'status'  => '200 OK',
            'version' => 'HTTP/1.1',
            'headers' => [
                'Content-Type' => 'application/hap+json'
            ],
            'body' => $body
        ]);
    }

    private function getAccessories($http)
    {
        $response = [
            'accessories' => $this->manager->getAccessories()
        ];

        return $this->buildEncryptedResponse($this->buildAccessoriesResponse(json_encode($response)));
    }
}
