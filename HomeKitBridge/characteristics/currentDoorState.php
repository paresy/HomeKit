<?php

declare(strict_types=1);

class HAPCharacteristicCurrentDoorState extends HAPCharacteristic
{
    public const Open = 0;
    public const Closed = 1;
    public const Opening = 2;
    public const Closing = 3;
    public const Stopped = 4;

    public function __construct()
    {
        parent::__construct(
            0x0E,
            HAPCharacteristicFormat::UnsignedInt8,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ],
            0,
            4,
            1
        );
    }
}
