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
                new HAPCharacteristicCurrentAirPurifierState(),
                new HAPCharacteristicTargetAirPurifierState()
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
