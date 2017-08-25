<?php

declare(strict_types=1);

class HAPCharacteristicOpticalZoom extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0x11C,
            HAPCharacteristicFormat::Float,
            [
                HAPCharacteristicPermission::PairedWrite,
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ]
        );
    }
}
