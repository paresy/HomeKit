<?php

declare(strict_types=1);

class HAPCharacteristicLockTargetState extends HAPCharacteristic
{
    public const Unsecured = 0;
    public const Secured = 1;

    public function __construct()
    {
        parent::__construct(
            0x1E,
            HAPCharacteristicFormat::UnsignedInt8,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::PairedWrite,
                HAPCharacteristicPermission::Notify
            ],
            0,
            1,
            1
        );
    }
}
