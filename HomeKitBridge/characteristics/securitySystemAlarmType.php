<?php

class HAPCharacteristicSecuritySystemAlarmType extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0x8E,
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
