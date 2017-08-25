<?php

declare(strict_types=1);
class HAPCharacteristicObstructionDetected extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0x24,
            HAPCharacteristicFormat::Boolean,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ]
        );
    }
}
