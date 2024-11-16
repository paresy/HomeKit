<?php

declare(strict_types=1);

class HAPCharacteristicLockCurrentState extends HAPCharacteristic
{
    public const Unsecured = 0;
    public const Secured = 1;
    public const Jammed = 2;
    public const Unknown = 3;

    public function __construct()
    {
        parent::__construct(
            0x1D,
            HAPCharacteristicFormat::UnsignedInt8,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ],
            0,
            3,
            1
        );
    }
}
