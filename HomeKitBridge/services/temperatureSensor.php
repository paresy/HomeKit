<?

class HAPServiceTemperatureSensor extends HAPService {

    public function __construct() {

        parent::__construct(
            0x8A,
            Array(
                //Required Characteristics
                new HAPCharacteristicCurrentTemperature()
            ),
            Array(
                //Optional Characteristics
                new HAPCharacteristicName()
            )
        );

    }

}