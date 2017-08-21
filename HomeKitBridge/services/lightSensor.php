<?

class HAPServiceLighteSensor extends HAPService {

    public function __construct() {

        parent::__construct(
            0x8A,
            Array(
                //Required Characteristics
                new HAPCharacteristicCurrentAmbientLightLevel()
            ),
            Array(
                //Optional Characteristics
                new HAPCharacteristicName(),
                new HAPCharacteristicStatusActive(),
                new HAPCharacteristicStatusFault(),
                new HAPCharacteristicStatusTampered(),
                new HAPCharacteristicStatusLowBattery()
            )
        );

    }

}
