<?php

declare(strict_types=1);

class HAPCharacteristicTargetTiltAngle extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0xC2,
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
