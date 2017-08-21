<?

class HAPServiceHumiditySensor extends HAPService {

    public function __construct() {

        parent::__construct(
            0x82,
            Array(
                //Required Characteristics
                new HAPCharacteristicCurrentRelativeHumidity()
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
