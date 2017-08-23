<?php

class HAPCharacteristicImageRotation extends HAPCharacteristic
{
    const NoRotation = 0;
    const Rotated90DegreesRight = 90;
    const Rotated180DegreesRight = 180;
    const Rotated270DegreesRight = 270;

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
