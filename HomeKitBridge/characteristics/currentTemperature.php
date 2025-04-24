<?php

declare(strict_types=1);

class HAPCharacteristicCurrentTemperature extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0x11,
            HAPCharacteristicFormat::Float,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ],
            // This is not compliant with the specs, but it fixed a lot of trouble for users
            -100,
            100,
            0.1,
            HAPCharacteristicUnit::Celsius
        );
    }
}
