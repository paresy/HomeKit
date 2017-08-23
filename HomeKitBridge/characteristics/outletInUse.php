<?php

class HAPCharacteristicOutletInUse extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0x26,
            HAPCharacteristicFormat::Boolean,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ]
        );
    }
}
