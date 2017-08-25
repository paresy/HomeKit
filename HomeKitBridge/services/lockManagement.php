<?php

declare(strict_types=1);

class HAPServiceLockManagement extends HAPService
{
    public function __construct()
    {
        parent::__construct(
            0x44,
            [
                //Required Characteristics
                new HAPCharacteristicLockControlPoint(),
                new HAPCharacteristicVersion(),
            ],
            [
                //Optional Characteristics
                new HAPCharacteristicLogs(),
                new HAPCharacteristicAudioFeedback(),
                new HAPCharacteristicLockManagementAutoSecurityTimeout(),
                new HAPCharacteristicAdministratorOnlyAccess(),
                new HAPCharacteristicLockLastKnownAction(),
                new HAPCharacteristicCurrentDoorState(),
                new HAPCharacteristicMotionDetected()
            ]
        );
    }
}
