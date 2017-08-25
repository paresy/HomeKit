<?php

declare(strict_types=1);

class HAPCharacteristicManufacturer extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0x20,
            HAPCharacteristicFormat::String,
            [
                HAPCharacteristicPermission::PairedRead
            ]
        );
    }
}
