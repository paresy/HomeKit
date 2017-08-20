<?

class HAPServiceLightbulb extends HAPService {

    public function __construct() {

        parent::__construct(
            0x43,
            Array(
                //Required Characteristics
                new HAPCharacteristicOn()
            ),
            Array(
                //Optional Characteristics
                new HAPCharacteristicBrightness(),
                new HAPCharacteristicHue(),
                new HAPCharacteristicName(),
                new HAPCharacteristicSaturation(),
                new HAPCharacteristicColorTemperature()
            )
        );

    }

}