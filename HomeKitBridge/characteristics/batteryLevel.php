<?php

class HAPCharacteristicBatteryLevel extends HAPCharacteristic
{

    public function __construct()
    {
        parent::__construct(
            0x68,
            HAPCharacteristicFormat::UnsignedInt8,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ],
            0,
            100,
            1,
            HAPCharacteristicUnit::Percentage
        );
    }
}
