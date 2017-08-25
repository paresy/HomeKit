<?php

declare(strict_types=1);

class HAPServiceHumiditySensor extends HAPService
{
    public function __construct()
    {
        parent::__construct(
            0x82,
            [
                //Required Characteristics
                new HAPCharacteristicCurrentRelativeHumidity()
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
