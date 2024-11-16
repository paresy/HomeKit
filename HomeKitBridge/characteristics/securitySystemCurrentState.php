<?php

declare(strict_types=1);

class HAPCharacteristicSecuritySystemCurrentState extends HAPCharacteristic
{
    public const StayArm = 0;
    public const AwayArm = 1;
    public const NightArm = 2;
    public const Disarmed = 3;
    public const AlarmTriggered = 4;

    public function __construct()
    {
        parent::__construct(
            0x66,
            HAPCharacteristicFormat::UnsignedInt8,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ],
            0,
            4,
            1
        );
    }
}
