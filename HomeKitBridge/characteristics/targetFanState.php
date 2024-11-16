<?php

declare(strict_types=1);

class HAPCharacteristicTargetFanState extends HAPCharacteristic
{
    public const Manual = 0;
    public const Auto = 1;

    public function __construct()
    {
        parent::__construct(
            0xBF,
            HAPCharacteristicFormat::UnsignedInt8,
            [
                HAPCharacteristicPermission::PairedWrite,
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ],
            0,
            1,
            1
        );
    }
}
