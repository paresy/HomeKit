<?php

declare(strict_types=1);

class HAPServiceFan extends HAPService
{
    public function __construct()
    {
        parent::__construct(
            0x40,
            [
                //Required Characteristics
                new HAPCharacteristicOn()
            ],
            [
                //Optional Characteristics
                new HAPCharacteristicRotationDirection(),
                new HAPCharacteristicRotationSpeed(),
                new HAPCharacteristicName()
            ]
        );
    }
}
