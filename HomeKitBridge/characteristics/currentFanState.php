<?php

declare(strict_types=1);

class HAPCharacteristicCurrentFanState extends HAPCharacteristic
{
    const Inactive = 0;
    const Idle = 1;
    const BlowingAir = 2;

    public function __construct()
    {
        parent::__construct(
            0xAF,
            HAPCharacteristicFormat::UnsignedInt8,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ],
            0,
            2,
            1
        );
    }
}
