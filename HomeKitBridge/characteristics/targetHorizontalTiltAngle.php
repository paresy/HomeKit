<?php

declare(strict_types=1);

class HAPCharacteristicTargetHorizontalTiltAngle extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0x7B,
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
