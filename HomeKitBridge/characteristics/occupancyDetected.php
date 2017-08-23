<?php

class HAPCharacteristicOccupancyDetected extends HAPCharacteristic
{
    const OccupancyNotDetected = 0;
    const OccupancyDetected = 1;

    public function __construct()
    {
        parent::__construct(
            0x71,
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
