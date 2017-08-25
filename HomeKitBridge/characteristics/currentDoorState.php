<?php

declare(strict_types=1);
class HAPCharacteristicCurrentDoorState extends HAPCharacteristic
{
    const Open = 0;
    const Closed = 1;
    const Opening = 2;
    const Closing = 3;
    const Stopped = 4;

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
