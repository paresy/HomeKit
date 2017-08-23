<?php

class HAPCharacteristicCurrentAirPurifierState extends HAPCharacteristic
{
    const Inactive = 0;
    const Idle = 1;
    const PurifyingAir = 2;

    public function __construct()
    {
        parent::__construct(
            0xA9,
            HAPCharacteristicFormat::UnsignedInt8,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ],
            0,
            2,
            1
        );
    }
}
