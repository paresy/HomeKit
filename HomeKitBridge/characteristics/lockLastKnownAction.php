<?php

declare(strict_types=1);
class HAPCharacteristicLockLastKnownAction extends HAPCharacteristic
{
    const SecuredPhysicalMovementInterior = 0;
    const UnsecuredPhysicalMovementInterior = 1;
    const SecuredPhysicalMovementExterior = 2;
    const UnsecuredPhysicalMovementExterior = 3;
    const SecuredKeypad = 4;
    const UnsecuredKeypad = 5;
    const SecuredRemotely = 6;
    const UnsecuredRemotely = 7;
    const SecuredAutomaticTimeout = 8;

    public function __construct()
    {
        parent::__construct(
            0x1C,
            HAPCharacteristicFormat::UnsignedInt8,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ],
            0,
            8,
            1
        );
    }
}
