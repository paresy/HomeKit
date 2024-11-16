<?php

declare(strict_types=1);

class HAPCharacteristicLockPhysicalControls extends HAPCharacteristic
{
    public const ControlLockDisabled = 0;
    public const ControlLockEnabled = 1;

    public function __construct()
    {
        parent::__construct(
            0xA7,
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
