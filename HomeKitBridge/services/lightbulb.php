<?php

declare(strict_types=1);

class HAPServiceLightbulb extends HAPService
{
    public function __construct()
    {
        parent::__construct(
            0x43,
            [
                //Required Characteristics
                new HAPCharacteristicOn()
            ],
            [
                //Optional Characteristics
                new HAPCharacteristicBrightness(),
                new HAPCharacteristicHue(),
                new HAPCharacteristicName(),
                new HAPCharacteristicSaturation(),
                new HAPCharacteristicColorTemperature()
            ]
        );
    }
}
