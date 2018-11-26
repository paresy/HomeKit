<?php

declare(strict_types=1);

class HAPAccessoryCarbonDioxideSensor extends HAPAccessoryBase
{
    public function __construct($data)
    {
        parent::__construct(
            $data,
            [
                new HAPServiceAccessoryInformation(),
                new HAPServiceCarbonDioxideSensor()
            ]
        );
    }

    public function notifyCharacteristicCarbonDioxideDetected()
    {
        return [
            $this->data['VariableID']
        ];
    }

    public function readCharacteristicCarbonDioxideDetected()
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
            return HAPCharacteristicCarbonDioxideDetected::Abnormal;
        } else {
            return HAPCharacteristicCarbonDioxideDetected::Normal;
        }
    }
}

class HAPAccessoryConfigurationCarbonDioxideSensor
{
    public static function getPosition()
    {
        return 10;
    }

    public static function getCaption()
    {
        return 'Carbon Dioxide Sensor';
    }

    public static function getColumns()
    {
        return [
            [
                'label' => 'VariableID',
                'name'  => 'VariableID',
                'width' => '250px',
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
            'de' => [
                'Carbon Dioxide Sensor'     => 'Kohlendioxid Sensor',
                'VariableID'                => 'VariablenID',
                'Variable missing'          => 'Variable fehlt',
                'Bool required'             => 'Bool benötigt',
                'OK'                        => 'OK'
            ]
        ];
    }
}
HomeKitManager::registerAccessory('CarbonDioxideSensor');
