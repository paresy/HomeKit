<?php

declare(strict_types=1);

class HAPCharacteristicSmokeDetected extends HAPCharacteristic
{
    public const SmokeNotDetected = 0;
    public const SmokeDetected = 1;

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
