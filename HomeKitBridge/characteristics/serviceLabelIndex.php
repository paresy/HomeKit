<?php

declare(strict_types=1);
class HAPCharacteristicServiceLabelIndex extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0xCB,
            HAPCharacteristicFormat::UnsignedInt8,
            [
                HAPCharacteristicPermission::PairedRead
            ],
            1,
            null,
            1
        );
    }
}
