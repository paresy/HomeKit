<?php

declare(strict_types=1);

class HAPCharacteristicCoolingThresholdTemperature extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0x0D,
            HAPCharacteristicFormat::Float,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::PairedWrite,
                HAPCharacteristicPermission::Notify
            ],
            10,
            35,
            0.1,
            HAPCharacteristicUnit::Celsius
        );
    }
}
