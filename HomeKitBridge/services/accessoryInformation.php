<?php

declare(strict_types=1);

class HAPServiceAccessoryInformation extends HAPService
{
    public function __construct()
    {
        parent::__construct(
            0x3E,
            [
                //Required Characteristics
                new HAPCharacteristicIdentify(),
                new HAPCharacteristicManufacturer(),
                new HAPCharacteristicModel(),
                new HAPCharacteristicName(),
                new HAPCharacteristicSerialNumber(),
                new HAPCharacteristicFirmwareRevision()
            ],
            [
                //Optional Characteristics
                new HAPCharacteristicHardwareRevision(),
                new HAPCharacteristicAccessoryFlags()
            ]
        );
    }
}
