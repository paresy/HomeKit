<?php

declare(strict_types=1);

class HAPCharacteristicStreamingStatus extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0x120,
            HAPCharacteristicFormat::TLV8,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ]
            /* Todo TLV8 encoded list of supported parameters */
        );
    }
}
