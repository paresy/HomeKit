<?php

class HAPCharacteristicStatusFault extends HAPCharacteristic
{
    const NoFault = 0;
    const GeneralFault = 1;

    public function __construct()
    {
        parent::__construct(
            0x77,
            HAPCharacteristicFormat::UnsignedInt8,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ],
            0,
            1,
            1
        );
    }
}
