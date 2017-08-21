<?php

class HAPCharacteristicStatusActive extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0x75,
            HAPCharacteristicFormat::Boolean,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ]
        );
    }
}
