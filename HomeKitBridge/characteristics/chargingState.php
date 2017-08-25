<?php

declare(strict_types=1);
class HAPCharacteristicChargingState extends HAPCharacteristic
{
    const NotCharging = 0;
    const Charging = 1;
    const NotChargeable = 2;

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
