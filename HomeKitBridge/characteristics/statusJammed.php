<?php

class HAPCharacteristicStatusJammed extends HAPCharacteristic
{
    const NotJammed = 0;
    const Jammed = 1;

    public function __construct()
    {
        parent::__construct(
            0x78,
            HAPCharacteristicFormat::UnsignedInt8,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ],
            0,
            1,
            1
        );
    }
}
