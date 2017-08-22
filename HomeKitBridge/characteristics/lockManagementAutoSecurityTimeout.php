<?php

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
            HAPCharacteristicUnit::Seconds
        );
    }
}
