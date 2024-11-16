<?php

declare(strict_types=1);

class HAPCharacteristicAirQuality extends HAPCharacteristic
{
    public const Unknown = 0;
    public const Excellent = 1;
    public const Good = 2;
    public const Fair = 3;
    public const Inferior = 4;
    public const Poor = 5;

    public function __construct()
    {
        parent::__construct(
            0x95,
            HAPCharacteristicFormat::UnsignedInt8,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ],
            0,
            5,
            1
        );
    }
}
