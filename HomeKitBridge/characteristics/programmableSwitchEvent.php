<?php

class HAPCharacteristicProgrammableSwitchEvent extends HAPCharacteristic
{
    const SinglePress = 0;
    const DoublePress = 1;
    const LongPress = 2;

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
}
