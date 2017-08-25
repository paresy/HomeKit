<?php

declare(strict_types=1);

class HAPServiceWindowCovering extends HAPService
{
    public function __construct()
    {
        parent::__construct(
            0x8C,
            [
                //Required Characteristics
                new HAPCharacteristicTargetPosition(),
                new HAPCharacteristicCurrentPosition(),
                new HAPCharacteristicPositionState()
            ],
            [
                //Optional Characteristics
                new HAPCharacteristicName(),
                new HAPCharacteristicHoldPosition(),
                new HAPCharacteristicCurrentHorizontalTiltAngle(),
                new HAPCharacteristicTargetHorizontalTiltAngle(),
                new HAPCharacteristicCurrentVerticalTiltAngle(),
                new HAPCharacteristicTargetVerticalTiltAngle(),
                new HAPCharacteristicObstructionDetected()
            ]
        );
    }
}
