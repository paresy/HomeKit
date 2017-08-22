<?php

class HAPCharacteristicCurrentHorizontalTiltAngle extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0x6C,
            HAPCharacteristicFormat::Integer,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ],
            -90,
            90,
            1,
            HAPCharacteristicUnit::ArcDegrees
        );
    }
}
