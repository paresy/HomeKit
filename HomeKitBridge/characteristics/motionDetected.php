<?php

declare(strict_types=1);

class HAPCharacteristicMotionDetected extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0x22,
            HAPCharacteristicFormat::Boolean,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ]
        );
    }
}
