<?php

declare(strict_types=1);

class HAPCharacteristicLeakDetected extends HAPCharacteristic
{
    public const LeakNotDetected = 0;
    public const LeakDetected = 1;

    public function __construct()
    {
        parent::__construct(
            0x70,
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
