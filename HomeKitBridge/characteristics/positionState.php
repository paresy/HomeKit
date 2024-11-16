<?php

declare(strict_types=1);

class HAPCharacteristicPositionState extends HAPCharacteristic
{
    public const GoingToMinimum = 0;
    public const GoingToMaximum = 1;
    public const Stopped = 2;

    public function __construct()
    {
        parent::__construct(
            0x72,
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
