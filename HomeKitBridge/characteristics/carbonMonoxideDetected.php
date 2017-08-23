<?php

class HAPCharacteristicCarbonMonoxideDetected extends HAPCharacteristic
{
    const Normal = 0;
    const Abnormal = 1;

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
