<?php

declare(strict_types=1);

class HAPCharacteristicCurrentSlatState extends HAPCharacteristic
{
    public const Fixed = 0;
    public const Jammed = 1;
    public const Swinging = 2;

    public function __construct()
    {
        parent::__construct(
            0xAA,
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
