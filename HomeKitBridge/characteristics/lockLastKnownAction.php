<?php

declare(strict_types=1);

class HAPCharacteristicLockLastKnownAction extends HAPCharacteristic
{
    public const SecuredPhysicalMovementInterior = 0;
    public const UnsecuredPhysicalMovementInterior = 1;
    public const SecuredPhysicalMovementExterior = 2;
    public const UnsecuredPhysicalMovementExterior = 3;
    public const SecuredKeypad = 4;
    public const UnsecuredKeypad = 5;
    public const SecuredRemotely = 6;
    public const UnsecuredRemotely = 7;
    public const SecuredAutomaticTimeout = 8;

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
