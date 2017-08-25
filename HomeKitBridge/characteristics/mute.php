<?php

declare(strict_types=1);

class HAPCharacteristicMute extends HAPCharacteristic
{
    const MuteOff = 0;
    const MuteOn = 1;

    public function __construct()
    {
        parent::__construct(
            0x11A,
            HAPCharacteristicFormat::Boolean,
            [
                HAPCharacteristicPermission::PairedWrite,
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ]
        );
    }
}
