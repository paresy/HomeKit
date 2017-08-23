<?php

class HAPServiceFanV2 extends HAPService
{
    public function __construct()
    {
        parent::__construct(
            0xB7,
            [
                //Required Characteristics
                new HAPCharacteristicActive()
            ],
            [
                //Optional Characteristics
                new HAPCharacteristicName(),
                new HAPCharacteristicCurrentFanState(),
                new HAPCharacteristicTargetFanState(),
                new HAPCharacteristicRotationDirection(),
                new HAPCharacteristicRotationSpeed(),
                new HAPCharacteristicSwingMode(),
                new HAPCharacteristicLockPhysicalControls()
            ]
        );
    }
}
