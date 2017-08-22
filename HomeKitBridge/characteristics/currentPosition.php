<?php

class HAPCharacteristicCurrentPosition extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0x6D,
            HAPCharacteristicFormat::UnsignedInt8,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ],
            0,
            100,
            1,
            HAPCharacteristicUnit::Percentage
        );
    }
}
