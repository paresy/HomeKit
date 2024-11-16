<?php

declare(strict_types=1);

class HAPCharacteristicCurrentHeatingCoolingState extends HAPCharacteristic
{
    public const Off = 0;
    public const Heat = 1;
    public const Cool = 2;

    public function __construct()
    {
        parent::__construct(
            0x0F,
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
