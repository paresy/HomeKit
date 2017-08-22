<?php

class HAPCharacteristicVOCDensity extends HAPCharacteristic
{

    public function __construct()
    {
        parent::__construct(
            0xC8,
            HAPCharacteristicFormat::Float,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ],
            0,
            1000
        );
    }
}
