<?php

class HAPServiceSwitch extends HAPService
{
    public function __construct()
    {
        parent::__construct(
            0x49,
            [
                //Required Characteristics
                new HAPCharacteristicOn()
            ],
            [
                //Optional Characteristics
                new HAPCharacteristicName()
            ]
        );
    }
}
