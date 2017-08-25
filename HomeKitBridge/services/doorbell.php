<?php

declare(strict_types=1);

class HAPServiceDoorbell extends HAPService
{
    public function __construct()
    {
        parent::__construct(
            0x121,
            [
                //Required Characteristics
                new HAPCharacteristicProgrammableSwitchEvent()
            ],
            [
                //Optional Characteristics
                new HAPCharacteristicName(),
                new HAPCharacteristicVolume(),
                new HAPCharacteristicBrightness()
            ]
        );
    }
}
