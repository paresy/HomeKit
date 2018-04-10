<?php

declare(strict_types=1);

class HAPCharacteristicTemperatureDisplayUnits extends HAPCharacteristic
{
    const Celsius = 0;
    const Fahrenheit = 1;

    public function __construct()
    {
        parent::__construct(
            0x36,
            HAPCharacteristicFormat::UnsignedInt8,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::PairedWrite,
                HAPCharacteristicPermission::Notify
            ],
            0,
            1,
            1
        );
    }
}
