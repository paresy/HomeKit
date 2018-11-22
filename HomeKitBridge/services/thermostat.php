<?php

declare(strict_types=1);

class HAPServiceThermostat extends HAPService
{
    public function __construct()
    {
        parent::__construct(
            0x4A,
            [
                //Required Characteristics
                new HAPCharacteristicCurrentHeatingCoolingState(),
                new HAPCharacteristicTargetHeatingCoolingState(),
                new HAPCharacteristicCurrentTemperature(),
                new HAPCharacteristicTargetTemperature(),
                new HAPCharacteristicTemperatureDisplayUnits()
            ],
            [
                //Optional Characteristics
                new HAPCharacteristicCoolingThresholdTemperature(),
                new HAPCharacteristicCurrentRelativeHumidity(),
                new HAPCharacteristicHeatingThresholdTemperature(),
                new HAPCharacteristicName(),
                new HAPCharacteristicTargetRelativeHumidity()
            ]
        );
    }
}
