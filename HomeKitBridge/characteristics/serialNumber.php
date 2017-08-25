<?php

declare(strict_types=1);

class HAPCharacteristicSerialNumber extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0x30,
            HAPCharacteristicFormat::String,
            [
                HAPCharacteristicPermission::PairedRead
            ]
        );
    }
}
