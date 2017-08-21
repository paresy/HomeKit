<?php

class HAPServiceTemperatureSensor extends HAPService
{
    public function __construct()
    {
        parent::__construct(
            0x8A,
            [
                //Required Characteristics
                new HAPCharacteristicCurrentTemperature()
            ],
            [
                //Optional Characteristics
                new HAPCharacteristicName(),
                new HAPCharacteristicStatusActive(),
                new HAPCharacteristicStatusFault(),
                new HAPCharacteristicStatusLowBattery(),
                new HAPCharacteristicStatusTampered()
            ]
        );
    }
}
