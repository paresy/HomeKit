<?php

declare(strict_types=1);

class HAPServiceSecuritySystem extends HAPService
{
    public function __construct()
    {
        parent::__construct(
            0x7E,
            [
                //Required Characteristics
                new HAPCharacteristicSecuritySystemCurrentState(),
                new HAPCharacteristicSecuritySystemTargetState()
            ],
            [
                //Optional Characteristics
                new HAPCharacteristicName(),
                new HAPCharacteristicSecuritySystemAlarmType(),
                new HAPCharacteristicStatusFault(),
                new HAPCharacteristicStatusTampered()
            ]
        );
    }
}
