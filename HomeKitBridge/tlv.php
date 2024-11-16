<?php

declare(strict_types=1);

class TLVType
{
    public const Method = 0x00;
    public const Identifier = 0x01;
    public const Salt = 0x02;
    public const PublicKey = 0x03;
    public const Proof = 0x04;
    public const EncryptedData = 0x05;
    public const State = 0x06;
    public const Error = 0x07;
    public const RetryDelay = 0x08;
    public const Certificate = 0x09;
    public const Signature = 0x0A;
    public const Permissions = 0x0B;
    public const FragmentData = 0x0C;
    public const FragmentLast = 0x0D;
    public const Separator = 0xFF;
}

class TLV8
{
    private $type;  //TLVType
    private $value; //string

    public function __construct(string &$data)
    {
        $this->type = ord($data[0]);
        $length = ord($data[1]);
        $this->value = substr($data, 2, $length);
        $data = substr($data, $length + 2);

        //support multi packet values
        if ($length == 255) {
            //lets check if the following is a followup
            if (strlen($data) > 0 && ord($data[0]) == $this->type) {
                //this call merges all PublicKey packets recursively
                $this->value .= (new self($data))->getValue();
            }
        }
    }

    public function getType(): int
    {
        return $this->type;
    }

    protected function getValue(): string
    {
        return $this->value;
    }
}

class TLVMethod
{
    public const PairSetup = 0x00;
    public const PairSetupMFi = 0x01;
    public const PairVerify = 0x02;
    public const AddPairing = 0x03;
    public const RemovePairing = 0x04;
    public const ListPairings = 0x05;
}

class TLV8_Method extends TLV8
{
    public function getMethod(): int
    {
        return unpack('C', $this->getValue())[1];
    }
}

class TLV8_Identifier extends TLV8
{
    public function getIdentifier(): string
    {
        return $this->getValue();
    }
}

class TLV8_Salt extends TLV8
{
    public function getSalt(): string
    {
        return $this->getValue();
    }
}

class TLV8_PublicKey extends TLV8
{
    public function getPublicKey(): string
    {
        return $this->getValue();
    }
}

class TLV8_Proof extends TLV8
{
    public function getProof(): string
    {
        return $this->getValue();
    }
}

class TLV8_EncryptedData extends TLV8
{
    public function getEncryptedData(): string
    {
        return $this->getValue();
    }
}

class TLVState
{
    public const M1 = 0x01;
    public const M2 = 0x02;
    public const M3 = 0x03;
    public const M4 = 0x04;
    public const M5 = 0x05;
    public const M6 = 0x06;
}

class TLV8_State extends TLV8
{
    public function getState(): int
    {
        return unpack('C', $this->getValue())[1];
    }
}

class TLVError
{
    public const NA = 0x00;
    public const Unknown = 0x01;
    public const Authentication = 0x02;
    public const Backoff = 0x03;
    public const MaxPeers = 0x04;
    public const MaxTries = 0x05;
    public const Unavailable = 0x06;
    public const Busy = 0x07;
}

class TLV8_Error extends TLV8
{
    public function __toString(): string
    {
        switch ($this->getError()) {
            case TLVError::NA:
                return 'N/A Error';
            case TLVError::Unknown:
                return 'Unknown Error';
            case TLVError::Authentication:
                return 'Authentication Error';
            case TLVError::Backoff:
                return 'Backoff Error';
            case TLVError::MaxPeers:
                return 'MaxPeers Error';
            case TLVError::MaxTries:
                return 'MaxTries Error';
            default:
                return 'Undefined Error';
        }
    }
    public function getError(): int
    {
        return unpack('C', $this->getValue())[1];
    }
}

class TLV8_RetryDelay extends TLV8
{
    public function getRetryDelay(): int
    {
        return unpack('N', $this->getValue())[1];
    }
}

class TLV8_Certificate extends TLV8
{
    public function getCertificate(): string
    {
        return $this->getValue();
    }
}

class TLV8_Signature extends TLV8
{
    public function getSignature(): string
    {
        return $this->getValue();
    }
}

class TLVPermissions
{
    public const RegularUser = 0x00;
    public const Admin = 0x01;
}

class TLV8_Permissions extends TLV8
{
    public function __toString(): string
    {
        switch ($this->getPermissions()) {
            case TLVPermissions::RegularUser:
                return 'Regular User';
            case TLVPermissions::Admin:
                return 'Admin';
            default:
                return 'Undefined';
        }
    }
    public function getPermissions(): int
    {
        return unpack('C', $this->getValue())[1];
    }
}

class TLV8_Separator extends TLV8
{
}

class TLVParser
{
    private $tlvList = [];

    public function __construct(string $data)
    {
        while (strlen($data) > 0) {
            $this->tlvList[] = $this->parseTLV($data);
        }
    }

    public function getByType(int $type) /* TLV8 | null */
    {
        foreach ($this->tlvList as $tlv) {
            if ($tlv->getType() == $type) {
                return $tlv;
            }
        }

        return null;
    }

    private function parseTLV(string &$data): TLV8
    {
        switch (ord($data[0])) {
            case TLVType::Method:
                return new TLV8_Method($data);
            case TLVType::Identifier:
                return new TLV8_Identifier($data);
            case TLVType::Salt:
                return new TLV8_Salt($data);
            case TLVType::PublicKey:
                return new TLV8_PublicKey($data);
            case TLVType::Proof:
                return new TLV8_Proof($data);
            case TLVType::EncryptedData:
                return new TLV8_EncryptedData($data);
            case TLVType::State:
                return new TLV8_State($data);
            case TLVType::Error:
                return new TLV8_Error($data);
            case TLVType::RetryDelay:
                return new TLV8_RetryDelay($data);
            case TLVType::Certificate:
                return new TLV8_Certificate($data);
            case TLVType::Signature:
                return new TLV8_Signature($data);
            case TLVType::Permissions:
                return new TLV8_Permissions($data);
            case TLVType::Separator:
                return new TLV8_Separator($data);
            default:
                throw new Exception('Unsupported TLV');
        }
    }
}

class TLVBuilder
{
    public static function Method(int $method): string
    {
        return self::Base(TLVType::Method, chr($method));
    }

    public static function Identifier(string $identifier): string
    {
        return self::Base(TLVType::Identifier, $identifier);
    }

    public static function Salt(string $salt): string
    {
        return self::Base(TLVType::Salt, $salt);
    }

    public static function PublicKey(string $publicKey): string
    {
        $response = '';
        $publicKeys = str_split($publicKey, 255);
        foreach ($publicKeys as $publicKey) {
            $response .= self::Base(TLVType::PublicKey, $publicKey);
        }

        return $response;
    }

    public static function Proof(string $proof): string
    {
        return self::Base(TLVType::Proof, $proof);
    }

    public static function EncryptedData(string $encryptedData): string
    {
        return self::Base(TLVType::EncryptedData, $encryptedData);
    }

    public static function State(int $state): string
    {
        return self::Base(TLVType::State, chr($state));
    }

    public static function Error(int $error): string
    {
        return self::Base(TLVType::Error, chr($error));
    }

    public static function RetryDelay(int $retryDelay): string
    {
        return self::Base(TLVType::RetryDelay, pack('N', $retryDelay));
    }

    public static function Certificate(string $certificate): string
    {
        return self::Base(TLVType::Certificate, $certificate);
    }

    public static function Signature(string $signature): string
    {
        return self::Base(TLVType::Signature, $signature);
    }

    public static function Permissions(int $permissions): string
    {
        return self::Base(TLVType::Permissions, chr($permissions));
    }

    public static function Separator(): string
    {
        return self::Base(TLVType::Separator, '');
    }
    private static function Base(int $type, string $value): string
    {
        return chr($type) . chr(strlen($value)) . $value;
    }
}
