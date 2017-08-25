<?php

declare(strict_types=1);

class HAPCharacteristicHoldPosition extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0x6F,
            HAPCharacteristicFormat::Boolean,
            [
                HAPCharacteristicPermission::PairedWrite
            ]
        );
    }
}
