<?php

declare(strict_types=1);

class HAPCharacteristicServiceLabelNamespace extends HAPCharacteristic
{
    public const Dots = 0;
    public const ArabicNumerals = 1;

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
