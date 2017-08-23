<?php

class HAPCharacteristicTargetVerticalTiltAngle extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0x7D,
            HAPCharacteristicFormat::Integer,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::PairedWrite,
                HAPCharacteristicPermission::Notify
            ],
            -90,
            90,
            1,
            HAPCharacteristicUnit::ArcDegrees
        );
    }
}
