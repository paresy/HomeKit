<?php

declare(strict_types=1);
class HAPServiceAirQualitySensor extends HAPService
{
    public function __construct()
    {
        parent::__construct(
            0x8D,
            [
                //Required Characteristics
                new HAPCharacteristicAirQuality()
            ],
            [
                //Optional Characteristics
                new HAPCharacteristicName(),
                new HAPCharacteristicOzoneDensity(),
                new HAPCharacteristicNitrogenDioxideDensity(),
                new HAPCharacteristicSulphurDioxideDensity(),
                new HAPCharacteristicPM2_5Density(),
                new HAPCharacteristicPM10Density(),
                new HAPCharacteristicVOCDensity(),
                new HAPCharacteristicStatusActive(),
                new HAPCharacteristicStatusFault(),
                new HAPCharacteristicStatusTampered(),
                new HAPCharacteristicStatusLowBattery()
            ]
        );
    }
}
