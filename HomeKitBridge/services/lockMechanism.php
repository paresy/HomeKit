<?php

declare(strict_types=1);

class HAPServiceLockMechanism extends HAPService
{
    public function __construct()
    {
        parent::__construct(
            0x45,
            [
                //Required Characteristics
                new HAPCharacteristicLockCurrentState(),
                new HAPCharacteristicLockTargetState()
            ],
            [
                //Optional Characteristics
                new HAPCharacteristicName()
            ]
        );
    }
}
