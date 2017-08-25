<?php

declare(strict_types=1);
class HAPCharacteristicDigitalZoom extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0x11D,
            HAPCharacteristicFormat::Float,
            [
                HAPCharacteristicPermission::PairedWrite,
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ]
        );
    }
}
