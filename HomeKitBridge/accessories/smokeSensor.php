<?

class HAPAccessorySmokeSensor extends HAPAccessory {

    public function __construct($data) {

        parent::__construct(
            $data,
            Array(
                new HAPServiceAccessoryInformation(),
                new HAPServiceSmokeSensor()
            )
        );

    }

    public function setCharacteristicIdentify($value) {

        //TODO: We probably should send some event

    }

    public function getCharacteristicManufacturer() {

        return "Kai Schnittcher";

    }

    public function getCharacteristicModel() {

        return str_replace("HAPAccessory", "", get_class($this));

    }

    public function getCharacteristicName() {

        return $this->data["Name"];

    }

    public function getCharacteristicSerialNumber() {

        return "Undefined";

    }

    public function getCharacteristicSmokeDetected() {

        if(GetValue($this->data["VariableID"]))
            return HAPCharacteristicSmokeDetected::SmokeDetected;
        else
            return HAPCharacteristicSmokeDetected::SmokeNotDetected;

    }

  }

class HAPAccessoryConfigurationSmokeSensor {

    public static function getPosition() {

        return 50;

    }

    public static function getCaption() {

        return "Smoke Sensor";

    }

    public static function getColumns() {

        return Array(
            Array(
                "label" => "VariableID",
                "name" => "VariableID",
                "width" => "100px",
                "add" => 0,
                "edit" => Array(
                    "type" => "SelectVariable"
                )
            )
        );
    }

    public static function getStatus($data) {
        $targetVariable = IPS_GetVariable($data["VariableID"]);

        if($targetVariable['VariableType'] != 0 /* Boolean */) {
            return "Boolean required";
        }

        return "OK";

    }

}

HomeKitManager::registerAccessory("SmokeSensor");
