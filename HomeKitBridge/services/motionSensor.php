<?php

class HAPServiceMotionSensor extends HAPService
{
    public function __construct()
    {
        parent::__construct(
            0x85,
            [
                //Required Characteristics
                new HAPCharacteristicMotionDetected()
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
