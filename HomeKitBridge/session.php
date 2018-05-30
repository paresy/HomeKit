<?php

declare(strict_types=1);

include_once __DIR__ . '/tlv.php';
include_once __DIR__ . '/srp.php';

class HomeKitSession
{
    private $debug = null;
    private $subscribe = null;
    private $pairings = null;
    private $codes = null;
    private $manager = null;
    private $bridgeID = '';
    private $accessoryKP = '';
    private $terminateSessions = null;

    //Flags for session
    private $empty = true;
    private $locked = false;

    //Data handling
    private $data = '';

    //Identifier for this session
    private $identifier = '';

    //Data encryption
    private $encrypted = false;
    private $encryptedData = '';
    private $messageRecvKey = '';
    private $messageRecvCounter = 0;
    private $messageSendKey = '';
    private $messageSendCounter = 0;

    //Event subscriptions
    private $events = [];

    //Required for stage PS M1+M3
    private $salt = '';
    private $setupCode = '';
    private $privateValue = '';
    private $publicValue = '';

    //Required for stage PS M5
    private $sharedSecret = '';

    //Required for stage PV M1+M3
    private $sessionKey = '';

    private function SendDebug(string $message)
    {
        ($this->debug)('HomeKitSession', $message, 0);
    }

    private function SubscribeVariable(int $variableID)
    {
        ($this->subscribe)($variableID);
    }

    public function __construct(callable $debug, callable $subscribe, HomeKitPairings $pairings, HomeKitCodes $codes, HomeKitManager $manager, string $bridgeID, string $accessoryKP, string $sessionData, callable $terminateSessions)
    {
        $this->debug = $debug;
        $this->subscribe = $subscribe;
        $this->pairings = $pairings;
        $this->codes = $codes;
        $this->manager = $manager;
        $this->bridgeID = $bridgeID;
        $this->accessoryKP = $accessoryKP;
        $this->terminateSessions = $terminateSessions;

        //Decode session data which is JSON encoded
        if ($sessionData != '') {
            $json = json_decode($sessionData, true);

            $this->empty = false;
            $this->locked = $json['locked'];

            //Copy data
            $this->data = hex2bin($json['data']);

            //Copy identifier
            $this->identifier = $json['identifier'];

            //Copy events
            $this->events = $json['events'];

            //Copy encryption
            $this->encrypted = $json['encrypted'];
            $this->encryptedData = hex2bin($json['encryptedData']);
            $this->messageRecvKey = hex2bin($json['messageRecvKey']);
            $this->messageRecvCounter = $json['messageRecvCounter'];
            $this->messageSendKey = hex2bin($json['messageSendKey']);
            $this->messageSendCounter = $json['messageSendCounter'];

            //Required for stage PS M1+M3
            $this->setupCode = $json['setupCode'];
            $this->salt = hex2bin($json['salt']);
            $this->privateValue = hex2bin($json['privateValue']);
            $this->publicValue = hex2bin($json['publicValue']);

            //Required for stage PS M5 and PV M1+M3
            $this->sharedSecret = hex2bin($json['sharedSecret']);

            //Required for stage PV M1+M3
            $this->sessionKey = hex2bin($json['sessionKey']);
        }
    }

    public function __toString(): string
    {
        return json_encode([
            'locked'             => $this->locked,
            'data'               => bin2hex($this->data),
            'identifier'         => $this->identifier,
            'events'             => $this->events,
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

    public function processData($data): string
    {
        //Bail out if session is locked
        if ($this->locked) {
            return $this->buildHTTP([
                'status'  => '400 Bad Request',
                'version' => 'HTTP/1.1',
                'headers' => null,
                'body'    => json_encode(['status' => -70401])
            ]);
        }

        //If the session is in an encrypted state we need to decrypt first
        if ($this->encrypted) {
            //Append data
            $this->encryptedData .= $data;

            if (strlen($this->encryptedData) < 2) {
                $this->sendDebug('Waiting for data...');

                return '';
            }

            $expectedLength = unpack('v', $this->encryptedData)[1];
            if (strlen($this->encryptedData) < $expectedLength + 2 /* Length */ + 16 /* AuthTag */) {
                $this->sendDebug('Waiting for data... ' . strlen($this->encryptedData) . ' / ' . $expectedLength);

                return '';
            }

            $message = substr($this->encryptedData, 2, $expectedLength + 16);
            $ad = substr($this->encryptedData, 0, 2);

            if (PHP_INT_SIZE == 4) {
                $nonce = "\0\0\0\0" . pack('V', $this->messageRecvCounter) . "\0\0\0\0";
            } else {
                $nonce = "\0\0\0\0" . pack('P', $this->messageRecvCounter);
            }

            $decryptedData = sodium_crypto_aead_chacha20poly1305_ietf_decrypt($message, $ad, $nonce, $this->messageRecvKey);
            if ($decryptedData === null) {
                $this->sendDebug('Decrypting failed!');

                //FIXME: We need to invalidate the whole session!

                return '';
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
        $http = $this->parseHTTP($this->data);
        if (count($http) == 0) {
            $this->SendDebug('Incomplete HTTP packet');

            //If we have an incomplete packet and the session is empty we probably lost the session
            //This might happen during runtime on module updates. Notify...
            if ($this->empty) {
                $this->SendDebug('We probably lost the session...');
                return $this->buildHTTP([
                    'status'  => '500 Internal Server Error',
                    'version' => 'HTTP/1.1',
                    'headers' => null,
                    'body'    => null
                ]);
            }

            return '';
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
                        return $this->readCharacteristics($http);
                    default:
                        $this->SendDebug('Unsupported uri for GET: ' . $http['uri']);

                        return '';
                }
                break;
            case 'PUT':
                switch ($http['uri']) {
                    case '/characteristics':
                        return $this->writeCharacteristics($http);
                    default:
                        $this->SendDebug('Unsupported uri for PUT: ' . $http['uri']);

                        return '';
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
                        $this->SendDebug('Unsupported uri for POST: ' . $http['uri']);

                        return '';
                }
            default:
                $this->SendDebug('Unsupported method ' . $http['method']);

                return '';
        }
    }

    private function parseHTTP($data): array
    {

        //Check for complete header
        $headerEnd = strpos($data, "\r\n\r\n");
        if ($headerEnd === false) {
            $this->SendDebug('Header is incomplete');

            return [];
        }

        $rawHeaders = explode("\r\n", substr($data, 0, $headerEnd));

        //Check for at least one array item
        if (count($rawHeaders) == 0) {
            $this->SendDebug('Header is empty');

            return [];
        }

        $top = explode(' ', $rawHeaders[0]);

        //Check if the first header line matches requirements
        if (count($top) != 3) {
            $this->SendDebug('Invalid first header line');

            return [];
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
        if ($method == 'GET' || $uri == '/identify' /* This is an exception from the rule! */) {
            $body = null;
        } else {
            //Content-Length is required
            if (!isset($headers['content-length'])) {
                $this->SendDebug('Content-Length missing');

                return [];
            }

            //Check if the body is complete
            $body = substr($data, $headerEnd + 4);
            if (strlen($body) != intval($headers['content-length'])) {
                $this->SendDebug('Content length does not match. Actual: ' . strlen($body) . ', Expected: ' . intval($headers['content-length']));

                return [];
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

    private function buildHTTP($http): string
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

    private function buildPairingResponse($body): string
    {

        //A fatal error occoured. Forward null
        if ($body === null) {
            return '';
        }

        return $this->buildHTTP([
            'status'  => '200 OK',
            'version' => 'HTTP/1.1',
            'headers' => [
                'Content-Type' => 'application/pairing+tlv8'
            ],
            'body' => $body
        ]);
    }

    private function postPairSetup($http): string
    {
        $tlvs = new TLVParser($http['body']);

        $tlvState = $tlvs->getByType(TLVType::State);

        //State is required
        if (!$tlvState || !($tlvState instanceof TLV8_State)) {
            $this->SendDebug('State missing');

            return '';
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

                return '';
        }
    }

    private function handlePairSetupM1(TLVParser $tlvs): string
    {
        $response = '';

        $tlvMethod = $tlvs->getByType(TLVType::Method);
        if (!$tlvMethod || !($tlvMethod instanceof TLV8_Method) || $tlvMethod->getMethod() != TLVMethod::PairSetup) {
            $this->SendDebug('Method missing or not PairSetup');
            $response .= TLVBuilder::State(TLVState::M2);
            $response .= TLVBuilder::Error(TLVError::Unknown);

            return $response;
        }

        if ($this->pairings->hasPairings()) {
            $this->SendDebug('Only one pairing is only allowed!');
            $response .= TLVBuilder::State(TLVState::M2);
            $response .= TLVBuilder::Error(TLVError::Unavailable);

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
            $this->SendDebug('No active SetupCode was found. Creating new...');
            $this->codes->generateSetupCode();
            $this->setupCode = $this->codes->getSetupCode();
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

    private function handlePairSetupM3(TLVParser $tlvs): string
    {
        $response = '';

        $tlvError = $tlvs->getByType(TLVType::Error);
        if ($tlvError || ($tlvError instanceof TLV8_Error)) {
            $this->SendDebug('Error while PairSetup M3: ' . $tlvError->getError());

            return '';
        }

        $tlvPublicKey = $tlvs->getByType(TLVType::PublicKey);
        $tlvProof = $tlvs->getByType(TLVType::Proof);
        if (!$tlvPublicKey || !($tlvPublicKey instanceof TLV8_PublicKey) || !$tlvProof || !($tlvProof instanceof TLV8_Proof)) {
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

    private function handlePairSetupM5(TLVParser $tlvs): string
    {
        $response = '';

        $tlvEncryptedData = $tlvs->getByType(TLVType::EncryptedData);

        //EncryptedData is required
        if (!$tlvEncryptedData || !($tlvEncryptedData instanceof TLV8_EncryptedData)) {
            $this->SendDebug('EncryptedData missing');

            return '';
        }

        $sessionKey = hash_hkdf('sha512', $this->sharedSecret, 32, 'Pair-Setup-Encrypt-Info', 'Pair-Setup-Encrypt-Salt');

        $decryptedData = sodium_crypto_aead_chacha20poly1305_ietf_decrypt($tlvEncryptedData->getEncryptedData(), '', "\0\0\0\0PS-Msg05", $sessionKey);

        //Verify that data is authentic
        if ($decryptedData === null) {
            $response .= TLVBuilder::State(TLVState::M6);
            $response .= TLVBuilder::Error(TLVError::Authentication);

            return $response;
        }

        $tlvs = new TLVParser($decryptedData);

        $tlvError = $tlvs->getByType(TLVType::Error);
        if ($tlvError || ($tlvError instanceof TLV8_Error)) {
            $this->SendDebug('Error while PairSetup M5: ' . $tlvError->getError());

            return '';
        }

        $tlvIdentifier = $tlvs->getByType(TLVType::Identifier);
        $tlvPublicKey = $tlvs->getByType(TLVType::PublicKey);
        $tlvSignature = $tlvs->getByType(TLVType::Signature);
        if (!$tlvIdentifier || !($tlvIdentifier instanceof TLV8_Identifier)
         || !$tlvPublicKey || !($tlvPublicKey instanceof TLV8_PublicKey)
         || !$tlvSignature || !($tlvSignature instanceof TLV8_Signature)) {
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
        $this->privateValue = '';
        $this->publicValue = '';
        $this->sharedSecret = '';

        return $response;
    }

    private function postPairVerify($http): string
    {
        $tlvs = new TLVParser($http['body']);

        $tlvState = $tlvs->getByType(TLVType::State);

        //State is required
        if (!$tlvState || !($tlvState instanceof TLV8_State)) {
            $this->SendDebug('State missing');

            return '';
        }

        switch ($tlvState->getState()) {
            case TLVState::M1:
                return $this->buildPairingResponse($this->handlePairVerifyM1($tlvs));
            case TLVState::M3:
                return $this->buildPairingResponse($this->handlePairVerifyM3($tlvs));
            default:
                $this->SendDebug('Unsupported pair verify state ' . $tlvState->getState());

                return '';
        }
    }

    private function handlePairVerifyM1(TLVParser $tlvs)
    {
        $response = '';

        $tlvPublicKey = $tlvs->getByType(TLVType::PublicKey);
        if (!$tlvPublicKey || !($tlvPublicKey instanceof TLV8_PublicKey)) {
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

    private function handlePairVerifyM3(TLVParser $tlvs)
    {
        $response = '';

        $tlvEncryptedData = $tlvs->getByType(TLVType::EncryptedData);

        //EncryptedData is required
        if (!$tlvEncryptedData || !($tlvEncryptedData instanceof TLV8_EncryptedData)) {
            $this->SendDebug('EncryptedData missing');

            return '';
        }

        $decryptedData = sodium_crypto_aead_chacha20poly1305_ietf_decrypt($tlvEncryptedData->getEncryptedData(), '', "\0\0\0\0PV-Msg03", $this->sessionKey);

        //Verify that data is authentic
        if ($decryptedData === null) {
            $this->SendDebug('Decrypting failed');
            $response .= TLVBuilder::State(TLVState::M4);
            $response .= TLVBuilder::Error(TLVError::Authentication);

            return $response;
        }

        $tlvs = new TLVParser($decryptedData);

        //Search if we know this PairingID
        $tlvIdentifier = $tlvs->getByType(TLVType::Identifier);

        //Identifier is required
        if (!$tlvIdentifier || !($tlvIdentifier instanceof TLV8_Identifier)) {
            $this->SendDebug('Identifier missing');

            return '';
        }

        $iOSDeviceLTPK = $this->pairings->getPairingPublicKey($tlvIdentifier->getIdentifier());
        if ($iOSDeviceLTPK === '') {
            $this->SendDebug('Identifier is invalid');
            $response .= TLVBuilder::State(TLVState::M4);
            $response .= TLVBuilder::Error(TLVError::Authentication);

            return $response;
        }

        //Get Signature
        $tlvSignature = $tlvs->getByType(TLVType::Signature);

        //Signature is required
        if (!$tlvSignature || !($tlvSignature instanceof TLV8_Signature)) {
            $this->SendDebug('Signature missing');

            return '';
        }

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
        $this->sessionKey = '';
        $this->sharedSecret = '';
        $this->privateValue = '';
        $this->publicValue = '';

        //Mark Session as encrypted
        $this->encrypted = true;
        $this->identifier = $tlvIdentifier->getIdentifier();
        $this->sendDebug('Session for ' . $this->identifier . ' is now using encrypted communication!');

        return $response;
    }

    private function buildEncryptedResponse(string $body): string
    {

        //Print debug
        $this->sendDebug('Encrypted data: ' . $body);

        //A fatal error occoured. Forward null
        if ($body === null) {
            return '';
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

    private function postPairings(array $http): string
    {
        if (!$this->encrypted) {
            return $this->buildHTTP([
                'status'  => '470 Connection Authorization Required',
                'version' => 'HTTP/1.1',
                'headers' => null,
                'body'    => null
            ]);
        }

        $tlvs = new TLVParser($http['body']);

        $tlvMethod = $tlvs->getByType(TLVType::Method);
        if (!$tlvMethod || !($tlvMethod instanceof TLV8_Method)) {
            $this->SendDebug('Method missing');

            return '';
        }

        $tlvState = $tlvs->getByType(TLVType::State);
        if (!$tlvState || !($tlvState instanceof TLV8_State)) {
            $this->SendDebug('State missing');

            return '';
        }

        if ($tlvState->getState() != TLVState::M1) {
            $this->SendDebug('State is not M1');

            return '';
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

                return '';
        }
    }

    private function handleAddPairing(TLVParser $tlvs): string
    {
        $response = '';

        if (!$this->encrypted) {
            $response .= TLVBuilder::State(TLVState::M2);
            $response .= TLVBuilder::Error(TLVError::Authentication);

            return $response;
        }

        if ($this->pairings->getPairingPermissions($this->identifier) != TLVPermissions::Admin) {
            $this->SendDebug('Permissions denied');
            $response .= TLVBuilder::State(TLVState::M2);
            $response .= TLVBuilder::Error(TLVError::Authentication);

            return $response;
        }

        $tlvIdentifier = $tlvs->getByType(TLVType::Identifier);
        if (!$tlvIdentifier || !($tlvIdentifier instanceof TLV8_Identifier)) {
            $this->SendDebug('Identifier is missing');
            $response .= TLVBuilder::State(TLVState::M2);
            $response .= TLVBuilder::Error(TLVError::Unknown);

            return $response;
        }

        $tlvPublicKey = $tlvs->getByType(TLVType::PublicKey);
        if (!$tlvPublicKey || !($tlvPublicKey instanceof TLV8_PublicKey)) {
            $this->SendDebug('PublicKey missing');
            $response .= TLVBuilder::State(TLVState::M2);
            $response .= TLVBuilder::Error(TLVError::Unknown);

            return $response;
        }

        $tlvPermissions = $tlvs->getByType(TLVType::Permissions);
        if (!$tlvPermissions || !($tlvPermissions instanceof TLV8_Permissions)) {
            $this->SendDebug('Permissions is missing');
            $response .= TLVBuilder::State(TLVState::M2);
            $response .= TLVBuilder::Error(TLVError::Unknown);

            return $response;
        }

        $this->pairings->addPairing($tlvIdentifier->getIdentifier(), $tlvPublicKey->getPublicKey(), $tlvPermissions->getPermissions());

        $response .= TLVBuilder::State(TLVState::M2);

        return $response;
    }

    private function handleRemovePairing(TLVParser $tlvs): string
    {
        $response = '';

        if (!$this->encrypted) {
            $response .= TLVBuilder::State(TLVState::M2);
            $response .= TLVBuilder::Error(TLVError::Authentication);

            return $response;
        }

        if ($this->pairings->getPairingPermissions($this->identifier) != TLVPermissions::Admin) {
            $this->SendDebug('Permissions denied');
            $response .= TLVBuilder::State(TLVState::M2);
            $response .= TLVBuilder::Error(TLVError::Authentication);

            return $response;
        }

        $tlvIdentifier = $tlvs->getByType(TLVType::Identifier);
        if (!$tlvIdentifier || !($tlvIdentifier instanceof TLV8_Identifier)) {
            $this->SendDebug('Identifier is missing');
            $response .= TLVBuilder::State(TLVState::M2);
            $response .= TLVBuilder::Error(TLVError::Unknown);

            return $response;
        }

        $this->pairings->removePairing($tlvIdentifier->getIdentifier());

        //Now tear down any connections for this identifier
        ($this->terminateSessions)($tlvIdentifier->getIdentifier());

        //Check remaining pairings if we ran out of admins
        $identifiers = $this->pairings->listPairings();
        $count = 0;
        foreach ($identifiers as $identifier) {
            if ($this->pairings->getPairingPermissions($identifier) == TLVPermissions::Admin) {
                $count++;
            }
        }
        if ($count == 0) {
            $this->SendDebug('No administrator is left. Unpair everyone!');
            foreach ($identifiers as $identifier) {
                ($this->terminateSessions)($identifier);
            }
            $this->pairings->clearPairings();
        }

        $response .= TLVBuilder::State(TLVState::M2);

        return $response;
    }

    private function handleListPairing(TLVParser $tlvs): string
    {
        $response = '';

        if (!$this->encrypted) {
            $response .= TLVBuilder::State(TLVState::M2);
            $response .= TLVBuilder::Error(TLVError::Authentication);

            return $response;
        }

        if ($this->pairings->getPairingPermissions($this->identifier) != TLVPermissions::Admin) {
            $this->SendDebug('Permissions denied');
            $response .= TLVBuilder::State(TLVState::M2);
            $response .= TLVBuilder::Error(TLVError::Authentication);

            return $response;
        }

        $identifiers = $this->pairings->listPairings();

        $response .= TLVBuilder::State(TLVState::M2);

        $first = true;
        foreach ($identifiers as $identifier) {
            if (!$first) {
                $response .= TLVBuilder::Separator();
            } else {
                $first = false;
            }
            $response .= TLVBuilder::Identifier($identifier);
            $response .= TLVBuilder::PublicKey($this->pairings->getPairingPublicKey($identifier));
            $response .= TLVBuilder::Permissions($this->pairings->getPairingPermissions($identifier));
        }

        return $response;
    }

    private function postIdentify(array $http): string
    {
        if ($this->pairings->hasPairings()) {
            return $this->buildHTTP([
                'status'  => '400 Bad Request',
                'version' => 'HTTP/1.1',
                'headers' => null,
                'body'    => json_encode(['status' => -70401])
            ]);
        } else {
            return $this->buildHTTP([
                'status'  => '204 No Content',
                'version' => 'HTTP/1.1',
                'headers' => null,
                'body'    => null
            ]);
        }
    }

    private function writeCharacteristics(array $http): string
    {
        if (!$this->encrypted) {
            return $this->buildHTTP([
                'status'  => '470 Connection Authorization Required',
                'version' => 'HTTP/1.1',
                'headers' => null,
                'body'    => null
            ]);
        }

        $characteristics = [];
        $ok = true;

        $data = json_decode($http['body'], true);
        if (isset($data['characteristics'])) {
            foreach ($data['characteristics'] as $characteristic) {
                if (isset($characteristic['value'])) {
                    if (!$this->manager->supportsWriteCharacteristics($characteristic['aid'], $characteristic['iid'])) {
                        $ok = false; //We need to send Multi-Status response
                        $characteristics[] = [
                            'aid'    => $characteristic['aid'],
                            'iid'    => $characteristic['iid'],
                            'status' => -70404
                        ];
                    } elseif ($this->manager->validateCharacteristics($characteristic['aid'], $characteristic['iid'], $characteristic['value']) != $characteristic['value']) {
                        $ok = false; //We need to send Multi-Status response
                        $characteristics[] = [
                            'aid'    => $characteristic['aid'],
                            'iid'    => $characteristic['iid'],
                            'status' => -70410
                        ];
                    } else {
                        $characteristics[] = [
                            'aid'    => $characteristic['aid'],
                            'iid'    => $characteristic['iid'],
                            'status' => 0
                        ];
                        $this->manager->writeCharacteristics($characteristic['aid'], $characteristic['iid'], $characteristic['value']);
                    }
                } elseif (isset($characteristic['ev'])) {
                    if (!$this->manager->supportsNotifyCharacteristics($characteristic['aid'], $characteristic['iid'])) {
                        $ok = false; //We need to send Multi-Status response
                        $characteristics[] = [
                            'aid'    => $characteristic['aid'],
                            'iid'    => $characteristic['iid'],
                            'status' => -70406
                        ];
                    } else {
                        $characteristics[] = [
                            'aid'    => $characteristic['aid'],
                            'iid'    => $characteristic['iid'],
                            'status' => 0
                        ];
                        if ($characteristic['ev']) {
                            $this->sendDebug('Registering Notify for Accessory ' . $characteristic['aid'] . ' with Instance ' . $characteristic['iid']);
                            $ids = $this->manager->notifyCharacteristics($characteristic['aid'], $characteristic['iid']);
                            if (count($ids) > 0) {
                                foreach ($ids as $id) {
                                    $this->subscribeVariable($id);
                                }
                                $this->events[$characteristic['aid']][$characteristic['iid']] = $ids;
                            }
                        } else {
                            if (isset($this->events[$characteristic['aid']]) && isset($this->events[$characteristic['aid']][$characteristic['iid']])) {
                                unset($this->events[$characteristic['aid']][$characteristic['iid']]);
                                if (count($this->events[$characteristic['aid']]) == 0) {
                                    unset($this->events[$characteristic['aid']]);
                                }
                            }
                            $this->sendDebug('Unregistering Notify for Accessory ' . $characteristic['aid'] . ' with Instance ' . $characteristic['iid']);
                        }
                    }
                } else {
                    $this->sendDebug('Unsupported write characteristic: ' . print_r($characteristic, true));
                }
            }
        }

        if ($ok) {
            return $this->buildEncryptedResponse($this->buildHTTP([
                'status'  => '204 No Content',
                'version' => 'HTTP/1.1',
                'headers' => null,
                'body'    => null
            ]));
        } else {
            return $this->buildEncryptedResponse($this->buildHTTP([
                'status'  => '207 Multi-Status',
                'version' => 'HTTP/1.1',
                'headers' => [
                    'Content-Type' => 'application/hap+json'
                ],
                'body' => json_encode([
                    'characteristics' => $characteristics
                ])
            ]));
        }
    }

    private function readCharacteristics(array $http): string
    {
        if (!$this->encrypted) {
            return $this->buildHTTP([
                'status'  => '470 Connection Authorization Required',
                'version' => 'HTTP/1.1',
                'headers' => null,
                'body'    => null
            ]);
        }

        $characteristics = [];
        $ok = true;

        $ids = explode(',', $http['query']['id']);

        foreach ($ids as $id) {
            $target = explode('.', $id);
            $aid = intval($target[0]);
            $iid = intval($target[1]);

            if (!$this->manager->supportsReadCharacteristics($aid, $iid)) {
                $ok = false; //We need to send Multi-Status response
                $characteristics[] = [
                    'aid'    => $aid,
                    'iid'    => $iid,
                    'status' => -70405
                ];
            } else {
                $value = $this->manager->readCharacteristics($aid, $iid);
                $validatedValue = $this->manager->validateCharacteristics($aid, $iid, $value);

                //Lets log this fixup behaviour...
                if ($value != $validatedValue) {
                    $this->SendDebug('Invalid characteristic value ' . $value . ' fixed to ' . $validatedValue);
                }

                $characteristics[] = [
                    'aid'   => $aid,
                    'iid'   => $iid,
                    'value' => $validatedValue
                ];
            }
        }

        return $this->buildEncryptedResponse($this->buildHTTP([
            'status'  => $ok ? '200 OK' : '207 Multi-Status',
            'version' => 'HTTP/1.1',
            'headers' => [
                'Content-Type' => 'application/hap+json'
            ],
            'body' => json_encode([
                'characteristics' => $characteristics
            ])
        ]));
    }

    private function buildAccessoriesResponse(string $body): string
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

    private function buildEventResponse(string $body): string
    {
        //Build the http response
        return $this->buildHTTP([
            'status'  => '200 OK',
            'version' => 'EVENT/1.0',
            'headers' => [
                'Content-Type' => 'application/hap+json'
            ],
            'body' => $body
        ]);
    }

    private function getAccessories(array $http): string
    {
        if (!$this->encrypted) {
            return $this->buildHTTP([
                'status'  => '470 Connection Authorization Required',
                'version' => 'HTTP/1.1',
                'headers' => null,
                'body'    => null
            ]);
        }

        $response = [
            'accessories' => $this->manager->getAccessories()
        ];

        return $this->buildEncryptedResponse($this->buildAccessoriesResponse(json_encode($response)));
    }

    private function sendNotify($aid, $iid, $value)
    {
        //we cannot use value directly as it might get post processed.
        //leave $value accessable if we decide to rewrite this somehow in the future
        return [
            'aid'   => $aid,
            'iid'   => $iid,
            'value' => $this->manager->readCharacteristics($aid, $iid) /*$value*/
        ];
    }

    public function notifyVariable($variableID, $value)
    {
        if (!$this->encrypted) {
            return null;
        }

        $characteristics = [];

        foreach ($this->events as $accessoryID => $instances) {
            foreach ($instances as $instanceID => $ids) {
                foreach ($ids as $id) {
                    if ($variableID == $id) {
                        $characteristics[] = $this->sendNotify($accessoryID, $instanceID, $value);
                    }
                }
            }
        }

        if (count($characteristics) == 0) {
            return null;
        }

        return $this->buildEncryptedResponse($this->buildEventResponse(json_encode([
            'characteristics' => $characteristics
        ])));
    }

    public function clearSubscriptions() {
        $this->events = [];
    }

}
