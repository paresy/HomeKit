<?php

declare(strict_types=1);

class HAPCharacteristicTargetDoorState extends HAPCharacteristic
{
    public const Open = 0;
    public const Closed = 1;

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
