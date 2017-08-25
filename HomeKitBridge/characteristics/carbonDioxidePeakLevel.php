<?php

declare(strict_types=1);

class HAPCharacteristicCarbonDioxidePeakLevel extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0x94,
            HAPCharacteristicFormat::Float,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ],
            0,
            100000
        );
    }
}
