<?php

declare(strict_types=1);

class HAPCharacteristicActive extends HAPCharacteristic
{
    const Inactive = 0;
    const Active = 1;

    public function __construct()
    {
        parent::__construct(
            0xB0,
            HAPCharacteristicFormat::UnsignedInt8,
            [
                HAPCharacteristicPermission::PairedWrite,
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ],
            0,
            1,
            1
        );
    }
}
