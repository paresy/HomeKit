<?php

declare(strict_types=1);

class HAPCharacteristicSupportedAudioStreamConfiguration extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0x115,
            HAPCharacteristicFormat::TLV8,
            [
                HAPCharacteristicPermission::PairedRead
            ]
            /* Todo TLV8 encoded list of supported parameters */
        );
    }
}
