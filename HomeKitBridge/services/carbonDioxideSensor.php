<?php

declare(strict_types=1);

class HAPServiceCarbonDioxideSensor extends HAPService
{
    public function __construct()
    {
        parent::__construct(
            0x97,
            [
                //Required Characteristics
                new HAPCharacteristicCarbonDioxideDetected()
            ],
            [
                //Optional Characteristics
                new HAPCharacteristicName(),
                new HAPCharacteristicStatusActive(),
                new HAPCharacteristicStatusFault(),
                new HAPCharacteristicStatusTampered(),
                new HAPCharacteristicStatusLowBattery(),
                new HAPCharacteristicCarbonDioxideLevel(),
                new HAPCharacteristicCarbonDioxidePeakLevel()
            ]
        );
    }
}
