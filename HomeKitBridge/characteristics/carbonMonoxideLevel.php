<?php

class HAPCharacteristicCarbonMonoxideLevel extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0x90,
            HAPCharacteristicFormat::Float,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ],
            0,
            100
        );
    }
}
