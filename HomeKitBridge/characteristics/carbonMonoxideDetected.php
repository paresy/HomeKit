<?php

declare(strict_types=1);

class HAPCharacteristicCarbonMonoxideDetected extends HAPCharacteristic
{
    public const Normal = 0;
    public const Abnormal = 1;

    public function __construct()
    {
        parent::__construct(
            0x69,
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
