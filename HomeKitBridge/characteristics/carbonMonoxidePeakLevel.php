<?php

class HAPCharacteristicCarbonMonoxidePeakLevel extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0x91,
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
