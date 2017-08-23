<?php

class HAPCharacteristicCurrentTiltAngle extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0xC1,
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
