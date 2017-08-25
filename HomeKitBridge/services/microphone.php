<?php

declare(strict_types=1);
class HAPServiceMicrophone extends HAPService
{
    public function __construct()
    {
        parent::__construct(
            0x112,
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
