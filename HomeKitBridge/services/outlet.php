<?php

class HAPServiceOutlet extends HAPService
{
    public function __construct()
    {
        parent::__construct(
            0x47,
            [
                //Required Characteristics
                new HAPCharacteristicOn(),
                new HAPCharacteristicOutletInUse()
            ],
            [
                //Optional Characteristics
                new HAPCharacteristicName()
            ]
        );
    }
}
