<?php

class HAPServiceSlat extends HAPService
{
    public function __construct()
    {
        parent::__construct(
            0xB9,
            [
                //Required Characteristics
                new HAPCharacteristicCurrentSlatState(),
                new HAPCharacteristicSlatType()
            ],
            [
                //Optional Characteristics
                new HAPCharacteristicName(),
                new HAPCharacteristicSwingMode(),
                new HAPCharacteristicCurrentTiltAngle(),
                new HAPCharacteristicTargetTiltAngle()
            ]
        );
    }
}
