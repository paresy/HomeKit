<?php

declare(strict_types=1);

class HAPCharacteristicTargetAirPurifierState extends HAPCharacteristic
{
    public const Manual = 0;
    public const Auto = 1;

    public function __construct()
    {
        parent::__construct(
            0xA8,
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
