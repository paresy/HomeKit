<?php

declare(strict_types=1);

class HAPAccessorySmokeSensor extends HAPAccessoryBase
{
    public function __construct($data)
    {
        parent::__construct(
            $data,
            [
                new HAPServiceAccessoryInformation(),
                new HAPServiceSmokeSensor()
            ]
        );
    }

    public function notifyCharacteristicSmokeDetected()
    {
        return [
            $this->data['VariableID']
        ];
    }

    public function readCharacteristicSmokeDetected()
    {
        $targetVariable = IPS_GetVariable($this->data['VariableID']);

        if ($targetVariable['VariableCustomProfile'] != '') {
            $profileName = $targetVariable['VariableCustomProfile'];
        } else {
            $profileName = $targetVariable['VariableProfile'];
        }

        $value = GetValue($this->data['VariableID']);

        //invert value if the variable profile is inverted
        if (strpos($profileName, '.Reversed') !== false) {
            $value = !$value;
        }

        if ($value) {
            return HAPCharacteristicSmokeDetected::SmokeDetected;
        } else {
            return HAPCharacteristicSmokeDetected::SmokeNotDetected;
        }
    }
}

class HAPAccessoryConfigurationSmokeSensor
{
    public static function getPosition()
    {
        return 50;
    }

    public static function getCaption()
    {
        return 'Smoke Sensor';
    }

    public static function getColumns()
    {
        return [
            [
                'label' => 'VariableID',
                'name'  => 'VariableID',
                'width' => '150px',
                'add'   => 0,
                'edit'  => [
                    'type' => 'SelectVariable'
                ]
            ]
        ];
    }

    public static function getStatus($data)
    {
        if (!IPS_VariableExists($data['VariableID'])) {
            return 'Variable missing';
        }

        $targetVariable = IPS_GetVariable($data['VariableID']);

        if ($targetVariable['VariableType'] != 0 /* Boolean */) {
            return 'Bool required';
        }

        return 'OK';
    }

    public static function getTranslations()
    {
        return [
            "de" => [
                "Smoke Sensor"         => "Rauchmelder",
                "VariableID"           => "VariablenID",
                "Variable missing"     => "Variable fehlt",
                "Bool required"        => "Bool benötigt",
                "OK"                   => "OK"
            ]
        ];
    }
}

HomeKitManager::registerAccessory('SmokeSensor');
