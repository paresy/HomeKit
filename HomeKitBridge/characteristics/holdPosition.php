<?php

class HAPCharacteristicHoldPosition extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0x6E,
            HAPCharacteristicFormat::Boolean,
            [
                HAPCharacteristicPermission::PairedWrite
            ]
        );
    }
}
