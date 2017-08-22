<?php

class HAPCharacteristicTargetDoorState extends HAPCharacteristic
{
    const Open = 0;
    const Closed = 1;

    public function __construct()
    {
        parent::__construct(
            0x32,
            HAPCharacteristicFormat::UnsignedInt8,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::PairedWrite,
                HAPCharacteristicPermission::Notify
            ],
            0,
            1,
            1
        );
    }
}
