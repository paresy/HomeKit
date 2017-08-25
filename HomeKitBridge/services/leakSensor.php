<?php

declare(strict_types=1);

class HAPServiceLeakSensor extends HAPService
{
    public function __construct()
    {
        parent::__construct(
            0x83,
            [
                //Required Characteristics
                new HAPCharacteristicLeakDetected()
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
