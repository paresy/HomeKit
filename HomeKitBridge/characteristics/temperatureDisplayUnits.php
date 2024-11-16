<?php

declare(strict_types=1);

class HAPCharacteristicTemperatureDisplayUnits extends HAPCharacteristic
{
    public const Celsius = 0;
    public const Fahrenheit = 1;

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
