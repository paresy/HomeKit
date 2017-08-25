<?php

declare(strict_types=1);

class HAPServiceGarageDoorOpener extends HAPService
{
    public function __construct()
    {
        parent::__construct(
            0x41,
            [
                //Required Characteristics
                new HAPCharacteristicCurrentDoorState(),
                new HAPCharacteristicTargetDoorState(),
                new HAPCharacteristicObstructionDetected()
            ],
            [
                //Optional Characteristics
                new HAPCharacteristicLockCurrentState(),
                new HAPCharacteristicLockTargetState(),
                new HAPCharacteristicName()
            ]
        );
    }
}
