<?php

declare(strict_types=1);

class HAPCharacteristicSlatType extends HAPCharacteristic
{
    public const Horizontal = 0;
    public const Vertical = 1;

    public function __construct()
    {
        parent::__construct(
            0xC0,
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
