<?php

declare(strict_types=1);

class HAPCharacteristicCurrentFanState extends HAPCharacteristic
{
    public const Inactive = 0;
    public const Idle = 1;
    public const BlowingAir = 2;

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
