<?

class HAPServiceLightbulb extends HAPService {

    public function __construct() {

        parent::__construct(
            0x49,
            Array(
                //Required Characteristics
                new HAPCharacteristicOn()
            ),
            Array(
                //Optional Characteristics
                new HAPCharacteristicName()
            )
        );

    }

}
