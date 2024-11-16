<?php

declare(strict_types=1);

class HAPCharacteristicSecuritySystemTargetState extends HAPCharacteristic
{
    public const StayArm = 0;
    public const AwayArm = 1;
    public const NightArm = 2;
    public const Disarm = 3;

    public function __construct()
    {
        parent::__construct(
            0x67,
            HAPCharacteristicFormat::UnsignedInt8,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::PairedWrite,
                HAPCharacteristicPermission::Notify
            ],
            0,
            3,
            1
        );
    }
}
