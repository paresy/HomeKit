<?php

class HAPServiceLockMechanisim extends HAPService
{
    public function __construct()
    {
        parent::__construct(
            0x45,
            [
                //Required Characteristics
                new HAPCharacteristicLockCurrentState(),
                new HAPCharacteristicTargetState()
            ],
            [
                //Optional Characteristics
                new HAPCharacteristicName()
            ]
        );
    }
}
