<?php

declare(strict_types=1);
class HAPCharacteristicBrightness extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0x08,
            HAPCharacteristicFormat::Integer,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::PairedWrite,
                HAPCharacteristicPermission::Notify
            ],
            0,
            100,
            1,
            HAPCharacteristicUnit::Percentage
        );
    }
}
