<?php

declare(strict_types=1);

class HAPCharacteristicCurrentRelativeHumidity extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0x10,
            HAPCharacteristicFormat::Float,
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
