<?php

class HAPCharacteristicCarbonDioxideLevel extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0x93,
            HAPCharacteristicFormat::Float,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ],
            0,
            100000
        );
    }
}
