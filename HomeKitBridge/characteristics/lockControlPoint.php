<?php

class HAPCharacteristicLockControlPoint extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0x19,
            HAPCharacteristicFormat::TLV8,
            [
                HAPCharacteristicPermission::PairedWrite
            ]
        );
    }
}
