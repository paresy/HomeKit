<?php

declare(strict_types=1);

class HAPCharacteristicStatusLowBattery extends HAPCharacteristic
{
    public const BatteryLevelNormal = 0;
    public const BatteryLevelLow = 1;

    public function __construct()
    {
        parent::__construct(
            0x79,
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
