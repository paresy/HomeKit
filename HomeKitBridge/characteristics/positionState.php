<?php

class HAPCharacteristicPositionState extends HAPCharacteristic
{
    const GoingToMinimum = 0;
    const GoingToMaximum = 1;
    const Stopped = 2;

    public function __construct()
    {
        parent::__construct(
            0x72,
            HAPCharacteristicFormat::UnsignedInt8,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ],
            0,
            2,
            1
        );
    }
}
