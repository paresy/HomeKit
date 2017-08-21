<?php

class TLVType
{
    const Method = 0x00;
    const Identifier = 0x01;
    const Salt = 0x02;
    const PublicKey = 0x03;
    const Proof = 0x04;
    const EncryptedData = 0x05;
    const State = 0x06;
    const Error = 0x07;
    const RetryDelay = 0x08;
    const Certificate = 0x09;
    const Signature = 0x0A;
    const Permissions = 0x0B;
    const FragmentData = 0x0C;
    const FragmentLast = 0x0D;
    const Seperator = 0xFF;
}

class TLV8
{
    private $type;  //TLVType
    private $value; //String

    public function __construct(&$data)
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

    public function getType()
    {
        return $this->type;
    }

    protected function getValue()
    {
        return $this->value;
    }
}

class TLVMethod
{
    const PairSetup = 0x00;
    const PairSetupMFi = 0x01;
    const PairVerify = 0x02;
    const AddPairing = 0x03;
    const RemovePairing = 0x04;
    const ListPairings = 0x05;
}

class TLV8_Method extends TLV8
{
    public function getMethod()
    {
        return unpack('C', $this->getValue())[1];
    }
}

class TLV8_Identifier extends TLV8
{
    public function getIdentifier()
    {
        return $this->getValue();
    }
}

class TLV8_Salt extends TLV8
{
    public function getSalt()
    {
        return $this->getValue();
    }
}

class TLV8_PublicKey extends TLV8
{
    public function getPublicKey()
    {
        return $this->getValue();
    }
}

class TLV8_Proof extends TLV8
{
    public function getProof()
    {
        return $this->getValue();
    }
}

class TLV8_EncryptedData extends TLV8
{
    public function getEncryptedData()
    {
        return $this->getValue();
    }
}

class TLVState
{
    const M1 = 0x01;
    const M2 = 0x02;
    const M3 = 0x03;
    const M4 = 0x04;
    const M5 = 0x05;
    const M6 = 0x06;
}

class TLV8_State extends TLV8
{
    public function getState()
    {
        return unpack('C', $this->getValue())[1];
    }
}

class TLVError
{
    const NA = 0x00;
    const Unknown = 0x01;
    const Authentication = 0x02;
    const Backoff = 0x03;
    const MaxPeers = 0x04;
    const MaxTries = 0x05;
    const Unavailable = 0x06;
    const Busy = 0x07;
}

class TLV8_Error extends TLV8
{
    public function getError()
    {
        return unpack('C', $this->getValue())[1];
    }

    public function __toString()
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
}

class TLV8_RetryDelay extends TLV8
{
    public function getRetryDelay()
    {
        return unpack('N', $this->getValue())[1];
    }
}

class TLV8_Certificate extends TLV8
{
    public function getCertificate()
    {
        return $this->getValue();
    }
}

class TLV8_Signature extends TLV8
{
    public function getSignature()
    {
        return $this->getValue();
    }
}

class TLVPermissions
{
    const RegularUser = 0x00;
    const Admin = 0x01;
}

class TLV8_Permissions extends TLV8
{
    public function getPermissions()
    {
        return unpack('C', $this->getValue())[1];
    }

    public function __toString()
    {
        switch ($this->getPermissions()) {
            case TLVPermissions::RegularUser:
                return 'Regular User';
            case TLVPermissions::Admin:
                return 'Admin';
            default:
                throw new Exception('Invalid permission');
        }
    }
}

class TLV8_Seperator extends TLV8
{
}

class TLVParser
{
    private $tlvList = [];

    public function __construct($data)
    {
        while (strlen($data) > 0) {
            $this->tlvList[] = $this->parseTLV($data);
        }
    }

    private function parseTLV(&$data)
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
            case TLVType::Seperator:
                return new TLV8_Seperator($data);
            default:
                return new TLV8($data);
        }
    }

    public function getByType($type)
    {
        foreach ($this->tlvList as $tlv) {
            if ($tlv->getType() == $type) {
                return $tlv;
            }
        }
    }
}

class TLVBuilder
{
    private static function Base($type, $value)
    {
        return chr($type) . chr(strlen($value)) . $value;
    }

    public static function Method($method)
    {
        return self::Base(TLVType::Method, chr($method));
    }

    public static function Identifier($identifier)
    {
        return self::Base(TLVType::Identifier, $identifier);
    }

    public static function Salt($salt)
    {
        return self::Base(TLVType::Salt, $salt);
    }

    public static function PublicKey($publicKey)
    {
        $response = '';
        $publicKeys = str_split($publicKey, 255);
        foreach ($publicKeys as $publicKey) {
            $response .= self::Base(TLVType::PublicKey, $publicKey);
        }

        return $response;
    }

    public static function Proof($proof)
    {
        return self::Base(TLVType::Proof, $proof);
    }

    public static function EncryptedData($encryptedData)
    {
        return self::Base(TLVType::EncryptedData, $encryptedData);
    }

    public static function State($state)
    {
        return self::Base(TLVType::State, chr($state));
    }

    public static function Error($error)
    {
        return self::Base(TLVType::Error, chr($error));
    }

    public static function RetryDelay($retryDelay)
    {
        return self::Base(TLVType::RetryDelay, pack('N', $retryDelay));
    }

    public static function Certificate($certificate)
    {
        return self::Base(TLVType::Certificate, $certificate);
    }

    public static function Signature($signature)
    {
        return self::Base(TLVType::Signature, $signature);
    }

    public static function Permissions($permissions)
    {
        return self::Base(TLVType::Permissions, chr($permissions));
    }

    public static function Seperator()
    {
        return self::Base(TLVType::Seperator, '');
    }
}
