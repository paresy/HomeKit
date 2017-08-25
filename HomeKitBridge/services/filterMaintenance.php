<?php

declare(strict_types=1);

class HAPServiceFilterMaintenance extends HAPService
{
    public function __construct()
    {
        parent::__construct(
            0xBA,
            [
                //Required Characteristics
                new HAPCharacteristicFilterChangeIndication()
            ],
            [
                //Optional Characteristics
                new HAPCharacteristicName(),
                new HAPCharacteristicFilterLifeLevel(),
                new HAPCharacteristicResetFilterIndication()
            ]
        );
    }
}
