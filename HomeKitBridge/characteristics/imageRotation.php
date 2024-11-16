<?php

declare(strict_types=1);

class HAPCharacteristicImageRotation extends HAPCharacteristic
{
    public const NoRotation = 0;
    public const Rotated90DegreesRight = 90;
    public const Rotated180DegreesRight = 180;
    public const Rotated270DegreesRight = 270;

    public function __construct()
    {
        parent::__construct(
            0x11E,
            HAPCharacteristicFormat::Float,
            [
                HAPCharacteristicPermission::PairedWrite,
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ]
        );
    }
}
