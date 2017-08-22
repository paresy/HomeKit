<?php

class HAPCharacteristicTargetTemperature extends HAPCharacteristic
{

    public function __construct()
    {
        parent::__construct(
            0x35,
            HAPCharacteristicFormat::Float,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::PairedWrite,
                HAPCharacteristicPermission::Notify
            ],
            10.0,
            38.0,
            0.1,
            HAPCharacteristicUnit::Celsius
        );
    }
}
