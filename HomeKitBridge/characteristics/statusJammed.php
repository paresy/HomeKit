<?php

declare(strict_types=1);

class HAPCharacteristicStatusJammed extends HAPCharacteristic
{
    public const NotJammed = 0;
    public const Jammed = 1;

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
