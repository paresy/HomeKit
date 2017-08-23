<?php

class HAPServiceAirPurifier extends HAPService
{
    public function __construct()
    {
        parent::__construct(
            0xBB,
            [
                //Required Characteristics
                new HAPCharacteristicActive(),
                new HAPCharacteristicCurrentAirPurifier(),
                new HAPCharacteristicTargetAirPurifier()
            ],
            [
                //Optional Characteristics
                new HAPCharacteristicName(),
                new HAPCharacteristicRotationSpeed(),
                new HAPCharacteristicSwingMode(),
                new HAPCharacteristicLockPhysicalControls()
            ]
        );
    }
}
