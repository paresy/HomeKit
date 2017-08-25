<?php

declare(strict_types=1);
class HAPCharacteristicPM2_5Density extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0xC6,
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
