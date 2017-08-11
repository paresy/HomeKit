<?

class HAPServiceAccessoryInformation extends HAPService {

    public function __construct() {

        parent::__construct(
            0x3E,
            Array(
                //Required Characteristics
                new HAPCharacteristicIdentify(),
                new HAPCharacteristicManufacturer(),
                new HAPCharacteristicModel(),
                new HAPCharacteristicName(),
                new HAPCharacteristicSerialNumber()
            ),
            Array(
                //Optional Characteristics
                new HAPCharacteristicFirmwareRevision(),
                new HAPCharacteristicHardwareRevision(),
                new HAPCharacteristicAccessoryFlags()
            )
        );
    }

}