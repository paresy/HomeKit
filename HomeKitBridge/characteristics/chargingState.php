<?php

declare(strict_types=1);

class HAPCharacteristicChargingState extends HAPCharacteristic
{
    public const NotCharging = 0;
    public const Charging = 1;
    public const NotChargeable = 2;

    public function __construct()
    {
        parent::__construct(
            0x8F,
            HAPCharacteristicFormat::UnsignedInt8,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ],
            0,
            2,
            1
        );
    }
}
