<?php

class HAPCharacteristicSecuritySystemCurrentState extends HAPCharacteristic
{
    const StayArm = 0;
    const AwayArm = 1;
    const NightArm = 2;
    const Disarmed = 3;
    const AlarmTriggerd = 4;

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
