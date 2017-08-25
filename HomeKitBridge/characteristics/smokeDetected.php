<?php

declare(strict_types=1);

class HAPCharacteristicSmokeDetected extends HAPCharacteristic
{
    const SmokeNotDetected = 0;
    const SmokeDetected = 1;

    public function __construct()
    {
        parent::__construct(
            0x76,
            HAPCharacteristicFormat::UnsignedInt8,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ],
            0,
            1,
            1
        );
    }
}
