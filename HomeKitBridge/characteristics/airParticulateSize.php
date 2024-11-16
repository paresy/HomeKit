<?php

declare(strict_types=1);

class HAPCharacteristicAirParticulateSize extends HAPCharacteristic
{
    public const Microsmeters2_5 = 0;
    public const Microsmeters10 = 1;

    public function __construct()
    {
        parent::__construct(
            0x65,
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
