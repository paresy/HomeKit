<?php

class HAPCharacteristicImageMirroring extends HAPCharacteristic
{
    const ImageNotMirrored = 0;
    const ImageMirrored = 1;

    public function __construct()
    {
        parent::__construct(
            0x11F,
            HAPCharacteristicFormat::Boolean,
            [
                HAPCharacteristicPermission::PairedWrite,
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ],
            HAPCharacteristicUnit::ArcDegrees
        );
    }
}
