<?php

class HAPCharacteristicCurrentSlatState extends HAPCharacteristic
{
    const Fixed = 0;
    const Jammed = 1;
    const Swinging = 2;

    public function __construct()
    {
        parent::__construct(
            0xA9,
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
