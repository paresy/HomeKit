<?php

class HAPCharacteristicServiceLabelNamespace extends HAPCharacteristic
{
    const Dots = 0;
    const ArabicNumerals = 1;

    public function __construct()
    {
        parent::__construct(
            0xCD,
            HAPCharacteristicFormat::UnsignedInt8,
            [
                HAPCharacteristicPermission::PairedRead
            ],
            0,
            1,
            1
        );
    }
}
