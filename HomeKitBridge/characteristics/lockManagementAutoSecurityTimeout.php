<?php

declare(strict_types=1);

class HAPCharacteristicLockManagementAutoSecurityTimeout extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0x1A,
            HAPCharacteristicFormat::UnsignedInt32,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::PairedWrite,
                HAPCharacteristicPermission::Notify
            ],
            null,
            null,
            null,
            HAPCharacteristicUnit::Seconds
        );
    }
}
