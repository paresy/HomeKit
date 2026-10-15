<?php

declare(strict_types=1);

//Simulates a HomeKit controller (e.g. an iOS device) talking to the bridge via the ServerSocket stub
class HomeKitTestController
{
    private $serverInterface;
    private $clientIP = '127.0.0.1';
    private $clientPort = 0;

    private $controllerID = 'AA:BB:CC:DD:EE:FF';
    private $controllerLTPK = '';
    private $controllerLTSK = '';
    private $accessoryLTPK = '';

    private $sendKey = '';
    private $recvKey = '';
    private $sendCounter = 0;
    private $recvCounter = 0;

    public function __construct(int $serverID)
    {
        $this->serverInterface = IPS\InstanceManager::getInstanceInterface($serverID);

        $keyPair = sodium_crypto_sign_keypair();
        $this->controllerLTPK = sodium_crypto_sign_publickey($keyPair);
        $this->controllerLTSK = sodium_crypto_sign_secretkey($keyPair);
    }

    //The setup code as shown in the pairing popup, e.g. 1234-5678
    public function pairSetup(string $displayCode, int $clientPort): void
    {
        $this->connect($clientPort);

        $digits = str_replace('-', '', $displayCode);
        $setupCode = substr($digits, 0, 3) . '-' . substr($digits, 3, 2) . '-' . substr($digits, 5, 3);

        //M1 -> M2
        $tlvs = $this->pairingRequest('/pair-setup', TLVBuilder::State(TLVState::M1) . TLVBuilder::Method(TLVMethod::PairSetup));
        $salt = $tlvs->getByType(TLVType::Salt)->getSalt();
        $B = $tlvs->getByType(TLVType::PublicKey)->getPublicKey();

        //M3 -> M4
        $srp = new SRP6aClient($salt, 'Pair-Setup', $setupCode, random_bytes(32));
        $A = $srp->createPublicValue();
        $K = $srp->createSessionKey($srp->createPresharedSecret($A, $B));
        $proof = $srp->createProof($A, $B, $K);

        $tlvs = $this->pairingRequest('/pair-setup', TLVBuilder::State(TLVState::M3) . TLVBuilder::PublicKey($A) . TLVBuilder::Proof($proof));
        if (!$srp->verifyProof($A, $proof, $K, $tlvs->getByType(TLVType::Proof)->getProof())) {
            throw new Exception('Accessory SRP proof is invalid');
        }

        //M5 -> M6
        $sessionKey = hash_hkdf('sha512', $K, 32, 'Pair-Setup-Encrypt-Info', 'Pair-Setup-Encrypt-Salt');
        $iOSDeviceX = hash_hkdf('sha512', $K, 32, 'Pair-Setup-Controller-Sign-Info', 'Pair-Setup-Controller-Sign-Salt');
        $signature = sodium_crypto_sign_detached($iOSDeviceX . $this->controllerID . $this->controllerLTPK, $this->controllerLTSK);

        $subTLVs = TLVBuilder::Identifier($this->controllerID) . TLVBuilder::PublicKey($this->controllerLTPK) . TLVBuilder::Signature($signature);
        $encrypted = sodium_crypto_aead_chacha20poly1305_ietf_encrypt($subTLVs, '', "\0\0\0\0PS-Msg05", $sessionKey);

        $tlvs = $this->pairingRequest('/pair-setup', TLVBuilder::State(TLVState::M5) . TLVBuilder::EncryptedData($encrypted));
        $decrypted = sodium_crypto_aead_chacha20poly1305_ietf_decrypt($tlvs->getByType(TLVType::EncryptedData)->getEncryptedData(), '', "\0\0\0\0PS-Msg06", $sessionKey);
        if (!is_string($decrypted)) {
            throw new Exception('Cannot decrypt M6');
        }

        $this->accessoryLTPK = (new TLVParser($decrypted))->getByType(TLVType::PublicKey)->getPublicKey();

        $this->disconnect();
    }

    //Establishes a secure session on a new connection
    public function pairVerify(int $clientPort): void
    {
        $this->connect($clientPort);

        $keyPair = sodium_crypto_box_keypair();
        $curveSK = sodium_crypto_box_secretkey($keyPair);
        $curvePK = sodium_crypto_box_publickey($keyPair);

        //M1 -> M2
        $tlvs = $this->pairingRequest('/pair-verify', TLVBuilder::State(TLVState::M1) . TLVBuilder::PublicKey($curvePK));
        $accessoryCurvePK = $tlvs->getByType(TLVType::PublicKey)->getPublicKey();

        $sharedSecret = sodium_crypto_scalarmult($curveSK, $accessoryCurvePK);
        $verifyKey = hash_hkdf('sha512', $sharedSecret, 32, 'Pair-Verify-Encrypt-Info', 'Pair-Verify-Encrypt-Salt');

        $decrypted = sodium_crypto_aead_chacha20poly1305_ietf_decrypt($tlvs->getByType(TLVType::EncryptedData)->getEncryptedData(), '', "\0\0\0\0PV-Msg02", $verifyKey);
        $subTLVs = new TLVParser($decrypted);
        $accessoryInfo = $accessoryCurvePK . $subTLVs->getByType(TLVType::Identifier)->getIdentifier() . $curvePK;
        if (!sodium_crypto_sign_verify_detached($subTLVs->getByType(TLVType::Signature)->getSignature(), $accessoryInfo, $this->accessoryLTPK)) {
            throw new Exception('Accessory signature is invalid');
        }

        //M3 -> M4
        $signature = sodium_crypto_sign_detached($curvePK . $this->controllerID . $accessoryCurvePK, $this->controllerLTSK);
        $subTLVs = TLVBuilder::Identifier($this->controllerID) . TLVBuilder::Signature($signature);
        $encrypted = sodium_crypto_aead_chacha20poly1305_ietf_encrypt($subTLVs, '', "\0\0\0\0PV-Msg03", $verifyKey);
        $this->pairingRequest('/pair-verify', TLVBuilder::State(TLVState::M3) . TLVBuilder::EncryptedData($encrypted));

        $this->sendKey = hash_hkdf('sha512', $sharedSecret, 32, 'Control-Write-Encryption-Key', 'Control-Salt');
        $this->recvKey = hash_hkdf('sha512', $sharedSecret, 32, 'Control-Read-Encryption-Key', 'Control-Salt');
        $this->sendCounter = 0;
        $this->recvCounter = 0;
    }

    //Sends a request via the secure session and returns the decoded JSON body
    public function request(string $method, string $uri, string $body = '')
    {
        $request = "$method $uri HTTP/1.1\r\nHost: bridge.local\r\n";
        if ($body !== '') {
            $request .= "Content-Type: application/hap+json\r\nContent-Length: " . strlen($body) . "\r\n";
        }
        $request .= "\r\n" . $body;

        $ad = pack('v', strlen($request));
        $nonce = "\0\0\0\0" . pack('P', $this->sendCounter++);
        $this->serverInterface->PushPacket($ad . sodium_crypto_aead_chacha20poly1305_ietf_encrypt($request, $ad, $nonce, $this->sendKey), $this->clientIP, $this->clientPort);

        return $this->parseHTTP($this->readEncrypted())['body'];
    }

    //Returns all event messages which were sent to this controller
    public function popEvents(): array
    {
        $events = [];
        while ($this->serverInterface->HasPacket()) {
            $events[] = $this->parseHTTP($this->readEncrypted())['body'];
        }
        return $events;
    }

    public function disconnect(): void
    {
        $this->serverInterface->PushDisconnect($this->clientIP, $this->clientPort);
    }

    private function connect(int $clientPort): void
    {
        $this->clientPort = $clientPort;
        $this->serverInterface->PushConnect($this->clientIP, $this->clientPort);
    }

    private function pairingRequest(string $uri, string $body): TLVParser
    {
        $request = "POST $uri HTTP/1.1\r\nHost: bridge.local\r\nContent-Type: application/pairing+tlv8\r\nContent-Length: " . strlen($body) . "\r\n\r\n" . $body;
        $this->serverInterface->PushPacket($request, $this->clientIP, $this->clientPort);

        $body = $this->parseHTTP($this->serverInterface->PopPacket()['Buffer'])['raw'];
        $tlvs = new TLVParser($body);

        $error = $tlvs->getByType(TLVType::Error);
        if ($error) {
            throw new Exception(sprintf('Pairing failed at %s with error %d', $uri, $error->getError()));
        }

        return $tlvs;
    }

    private function readEncrypted(): string
    {
        $data = $this->serverInterface->PopPacket()['Buffer'];

        $plain = '';
        while (strlen($data) > 0) {
            $ad = substr($data, 0, 2);
            $length = unpack('v', $ad)[1];
            $nonce = "\0\0\0\0" . pack('P', $this->recvCounter++);
            $frame = sodium_crypto_aead_chacha20poly1305_ietf_decrypt(substr($data, 2, $length + 16), $ad, $nonce, $this->recvKey);
            if (!is_string($frame)) {
                throw new Exception('Cannot decrypt frame');
            }
            $plain .= $frame;
            $data = substr($data, 2 + $length + 16);
        }

        return $plain;
    }

    private function parseHTTP(string $response): array
    {
        list($header, $body) = explode("\r\n\r\n", $response, 2);

        return [
            'status' => explode("\r\n", $header)[0],
            'raw'    => $body,
            'body'   => json_decode($body, true)
        ];
    }
}
