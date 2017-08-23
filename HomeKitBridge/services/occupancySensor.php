<?php

class HAPServiceOccupancySensor extends HAPService
{
    public function __construct()
    {
        parent::__construct(
            0x86,
            [
                //Required Characteristics
                new HAPCharacteristicOccupancyDetected()
            ],
            [
                //Optional Characteristics
                new HAPCharacteristicName(),
                new HAPCharacteristicStatusActive(),
                new HAPCharacteristicStatusFault(),
                new HAPCharacteristicStatusTampered(),
                new HAPCharacteristicStatusLowBattery()
            ]
        );
    }
}
