<?php

declare(strict_types=1);

class HAPCharacteristicSaturation extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0x2F,
            HAPCharacteristicFormat::Float,
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
