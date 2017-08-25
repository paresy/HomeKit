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
                new HAPCharacteristicSerialNumber()
            ],
            [
                //Optional Characteristics
                new HAPCharacteristicFirmwareRevision(),
                new HAPCharacteristicHardwareRevision(),
                new HAPCharacteristicAccessoryFlags()
            ]
        );
    }
}
