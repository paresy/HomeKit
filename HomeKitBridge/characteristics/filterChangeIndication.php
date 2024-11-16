<?php

declare(strict_types=1);

class HAPCharacteristicFilterChangeIndication extends HAPCharacteristic
{
    public const FilterOK = 0;
    public const FilterChangeRequired = 1;

    public function __construct()
    {
        parent::__construct(
            0xAC,
            HAPCharacteristicFormat::UnsignedInt8,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ],
            0,
            1,
            1
        );
    }
}
