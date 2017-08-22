<?php

class HAPCharacteristicNitrogenDioxideDensity extends HAPCharacteristic
{

    public function __construct()
    {
        parent::__construct(
            0xC4,
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
