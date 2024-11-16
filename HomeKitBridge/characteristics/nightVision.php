<?php

declare(strict_types=1);

class HAPCharacteristicNightVision extends HAPCharacteristic
{
    public const DisableMode = 0;
    public const EnableMode = 1;

    public function __construct()
    {
        parent::__construct(
            0x11B,
            HAPCharacteristicFormat::Boolean,
            [
                HAPCharacteristicPermission::PairedWrite,
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ]
        );
    }
}
