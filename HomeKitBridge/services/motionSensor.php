<?

class HAPServiceMotionsSensor extends HAPService {

    public function __construct() {

        parent::__construct(
            0x85,
            Array(
                //Required Characteristics
                new HAPCharacteristicMotionDetected()
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
