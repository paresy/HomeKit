<?php

class HAPServiceCarbonMonoxideSensor extends HAPService
{
    public function __construct()
    {
        parent::__construct(
            0x7F,
            [
                //Required Characteristics
                new HAPCharacteristicCarbonMonoxideDetected()
            ],
            [
                //Optional Characteristics
                new HAPCharacteristicName(),
                new HAPCharacteristicStatusActive(),
                new HAPCharacteristicStatusFault(),
                new HAPCharacteristicStatusTampered(),
                new HAPCharacteristicStatusLowBattery(),
                new HAPCharacteristicCarbonMonoxideLevel(),
                new HAPCharacteristicCarbonMonoxidePeakLevel()
            ]
        );
    }
}
