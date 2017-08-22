<?php

class HAPCharacteristicLockLogs extends HAPCharacteristic
{

    public function __construct()
    {
        parent::__construct(
            0x1F,
            HAPCharacteristicFormat::UnsignedTLV8,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ],
        );
    }
}
