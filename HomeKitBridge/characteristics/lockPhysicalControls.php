<?php

class HAPCharacteristicLockPhysicalControls extends HAPCharacteristic
{
    const ControlLockDisabled = 0;
    const ControlLockEnabled = 1;

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
