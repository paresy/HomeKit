<?php

declare(strict_types=1);

class HAPCharacteristicImageMirroring extends HAPCharacteristic
{
    public const ImageNotMirrored = 0;
    public const ImageMirrored = 1;

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
            null,
            null,
            null,
            HAPCharacteristicUnit::ArcDegrees
        );
    }
}
