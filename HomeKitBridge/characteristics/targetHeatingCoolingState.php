<?php

declare(strict_types=1);

class HAPCharacteristicTargetHeatingCoolingState extends HAPCharacteristic
{
    public const Off = 0;
    public const Heat = 1;
    public const Cool = 2;
    public const Auto = 3;

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
