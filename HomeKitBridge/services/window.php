<?php

declare(strict_types=1);

class HAPServiceWindow extends HAPService
{
    public function __construct()
    {
        parent::__construct(
            0x8B,
            [
                //Required Characteristics
                new HAPCharacteristicCurrentPosition(),
                new HAPCharacteristicTargetPosition(),
                new HAPCharacteristicPositionState()
            ],
            [
                //Optional Characteristics
                new HAPCharacteristicName(),
                new HAPCharacteristicHoldPosition(),
                new HAPCharacteristicObstructionDetected()
            ]
        );
    }
}
