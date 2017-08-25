<?php

declare(strict_types=1);
class HAPCharacteristicSetupEndpoints extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0x118,
            HAPCharacteristicFormat::TLV8,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::PairedWrite
            ]
            /* Todo TLV8 encoded list of supported parameters */
        );
    }
}
