<?php

declare(strict_types=1);
class HAPCharacteristicTargetHeatingCoolingState extends HAPCharacteristic
{
    const Off = 0;
    const Heat = 1;
    const Cool = 2;
    const Auto = 3;

    public function __construct()
    {
        parent::__construct(
            0x33,
            HAPCharacteristicFormat::UnsignedInt8,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::PairedWrite,
                HAPCharacteristicPermission::Notify
            ],
            0,
            3,
            1
        );
    }
}
