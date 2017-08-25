<?php

declare(strict_types=1);

class HAPServiceSpeaker extends HAPService
{
    public function __construct()
    {
        parent::__construct(
            0x113,
            [
                //Required Characteristics
                new HAPCharacteristicMute()
            ],
            [
                //Optional Characteristics
                new HAPCharacteristicName(),
                new HAPCharacteristicVolume()
            ]
        );
    }
}
