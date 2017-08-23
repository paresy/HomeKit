<?php

class HAPCharacteristicTargetPosition extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0x7C,
            HAPCharacteristicFormat::UnsignedInt8,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::PairedWrite,
                HAPCharacteristicPermission::Notify
            ],
            0,
            100,
            1,
            HAPCharacteristicUnit::Percentage
        );
    }
}
