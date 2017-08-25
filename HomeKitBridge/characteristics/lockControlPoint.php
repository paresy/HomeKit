<?php

declare(strict_types=1);

class HAPCharacteristicLockControlPoint extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0x19,
            HAPCharacteristicFormat::TLV8,
            [
                HAPCharacteristicPermission::PairedWrite
            ]
        );
    }
}
