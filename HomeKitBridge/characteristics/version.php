<?php

class HAPCharacteristicVersion extends HAPCharacteristic
{

    public function __construct()
    {
        parent::__construct(
            0x37,
            HAPCharacteristicFormat::String,
            [
                HAPCharacteristicPermission::PairedRead
            ],
            64 /* Maximum Length - Richtig, so? */
        );
    }
}
