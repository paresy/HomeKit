<?

class HAPServiceSmokeSensor extends HAPService {

    public function __construct() {

        parent::__construct(
            0x87,
            Array(
                //Required Characteristics
                new HAPCharacteristicSmokeDetected()
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
