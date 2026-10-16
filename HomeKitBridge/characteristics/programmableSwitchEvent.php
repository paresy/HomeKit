<?php

declare(strict_types=1);

class HAPCharacteristicProgrammableSwitchEvent extends HAPCharacteristic
{
    public const SinglePress = 0;
    public const DoublePress = 1;
    public const LongPress = 2;

    public function __construct()
    {
        parent::__construct(
            0x73,
            HAPCharacteristicFormat::UnsignedInt8,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ],
            0,
            2,
            1
        );
    }

    //Every press is an event, also when it is the same kind of press as before
    public function notifiesOnUpdate(): bool
    {
        return true;
    }
}
