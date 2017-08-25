<?php

declare(strict_types=1);
class HAPCharacteristicSecuritySystemTargetState extends HAPCharacteristic
{
    const StayArm = 0;
    const AwayArm = 1;
    const NightArm = 2;
    const Disarm = 3;

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
