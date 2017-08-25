<?php

declare(strict_types=1);
class HAPServiceLightSensor extends HAPService
{
    public function __construct()
    {
        parent::__construct(
            0x84,
            [
                //Required Characteristics
                new HAPCharacteristicCurrentAmbientLightLevel()
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
