<?php

declare(strict_types=1);

class HAPServiceContactSensor extends HAPService
{
    public function __construct()
    {
        parent::__construct(
            0x80,
            [
                //Required Characteristics
                new HAPCharacteristicContactSensorState()
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
