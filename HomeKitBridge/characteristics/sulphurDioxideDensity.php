<?php

declare(strict_types=1);

class HAPCharacteristicSulphurDioxideDensity extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0xC5,
            HAPCharacteristicFormat::Float,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ],
            0,
            1000
        );
    }
}
