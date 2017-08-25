<?php

declare(strict_types=1);

class HAPCharacteristicFilterLifeLevel extends HAPCharacteristic
{
    public function __construct()
    {
        parent::__construct(
            0xAB,
            HAPCharacteristicFormat::Float,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ],
            0,
            100,
            1
        );
    }
}
