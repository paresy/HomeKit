<?php

declare(strict_types=1);

class HAPCharacteristicContactSensorState extends HAPCharacteristic
{
    const ContactDetected = 0;
    const ContactNotDetected = 1;

    public function __construct()
    {
        parent::__construct(
            0x6A,
            HAPCharacteristicFormat::UnsignedInt8,
            [
                HAPCharacteristicPermission::PairedRead,
                HAPCharacteristicPermission::Notify
            ],
            0,
            1,
            1
        );
    }
}
