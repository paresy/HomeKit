<?php

declare(strict_types=1);
class HAPServiceBatteryService extends HAPService
{
    public function __construct()
    {
        parent::__construct(
            0x96,
            [
                //Required Characteristics
                new HAPCharacteristicBatteryLevel(),
                new HAPCharacteristicChargingState(),
                new HAPCharacteristicStatusLowBattery()
            ],
            [
                //Optional Characteristics
                new HAPCharacteristicName()
            ]
        );
    }
}
