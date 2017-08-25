<?php

declare(strict_types=1);

class HAPCharacteristicSupportedRTPConfiguration extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0x116,
            HAPCharacteristicFormat::TLV8,
            [
                HAPCharacteristicPermission::PairedRead
            ]
            /* Todo TLV8 encoded list of supported parameters */
        );
    }
}
