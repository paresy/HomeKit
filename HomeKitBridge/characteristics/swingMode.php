<?php

declare(strict_types=1);
class HAPCharacteristicSwingMode extends HAPCharacteristic
{
    const SwingDisabled = 0;
    const SwingEnabled = 0;

    public function __construct()
    {
        parent::__construct(
            0xB6,
            HAPCharacteristicFormat::UnsignedInt8,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify,
                HAPCharacteristicPermission::PairedWrite
            ],
            0,
            1,
            1
        );
    }
}
