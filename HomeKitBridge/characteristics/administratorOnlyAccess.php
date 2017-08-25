<?php

declare(strict_types=1);

class HAPCharacteristicAdministratorOnlyAccess extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0x01,
            HAPCharacteristicFormat::Boolean,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::PairedWrite,
                HAPCharacteristicPermission::Notify
            ]
        );
    }
}
