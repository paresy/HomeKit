<?php

declare(strict_types=1);
class HAPCharacteristicAirParticulateDensity extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0x64,
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
