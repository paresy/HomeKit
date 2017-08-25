<?php

declare(strict_types=1);
class HAPCharacteristicAirQuality extends HAPCharacteristic
{
    const Unknown = 0;
    const Excellent = 1;
    const Good = 2;
    const Fair = 3;
    const Inferior = 4;
    const Poor = 5;

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
