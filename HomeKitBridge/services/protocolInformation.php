<?php

declare(strict_types=1);

class HAPServiceProtocolInformation extends HAPService
{
    public function __construct()
    {
        parent::__construct(
            0xA2,
            [
                //Required Characteristics
                new HAPCharacteristicVersion(),
            ],
            [
                //Optional Characteristics
            ]
        );
    }
}
