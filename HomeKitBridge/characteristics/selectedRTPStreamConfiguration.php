<?php

class HAPCharacteristicSelectedRTPStreamConfiguration extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0x117,
            HAPCharacteristicFormat::TLV8,
            [
                HAPCharacteristicPermission::PairedWrite
            ]
            /* Todo TLV8 encoded list of supported parameters */
        );
    }
}
