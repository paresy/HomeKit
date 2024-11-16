<?php

declare(strict_types=1);

class HAPCharacteristicRotationDirection extends HAPCharacteristic
{
    public const Clockwise = 0;
    public const CounterClockwise = 1;

    public function __construct()
    {
        parent::__construct(
            0x28,
            HAPCharacteristicFormat::Integer,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::PairedWrite,
                HAPCharacteristicPermission::Notify
            ],
            0,
            1,
            1
        );
    }
}
