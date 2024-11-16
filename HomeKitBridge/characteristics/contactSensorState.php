<?php

declare(strict_types=1);

class HAPCharacteristicContactSensorState extends HAPCharacteristic
{
    public const ContactDetected = 0;
    public const ContactNotDetected = 1;

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
