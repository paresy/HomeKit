<?php

declare(strict_types=1);

class HAPCharacteristicOn extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0x25,
            HAPCharacteristicFormat::Boolean,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::PairedWrite,
                HAPCharacteristicPermission::Notify
            ]
        );
    }
}
