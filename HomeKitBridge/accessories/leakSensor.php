<?php

declare(strict_types=1);

class HAPAccessoryLeakSensor extends HAPAccessoryBase
{
    public function __construct($data)
    {
        parent::__construct(
            $data,
            [
                new HAPServiceAccessoryInformation(),
                new HAPServiceLeakSensor()
            ]
        );
    }

    public function notifyCharacteristicLeakDetected()
    {
        return [
            $this->data['VariableID']
        ];
    }

    public function readCharacteristicLeakDetected()
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

        if($value) {
            return HAPCharacteristicLeakDetected::LeakDetected;
        } else {
            return HAPCharacteristicLeakDetected::LeakNotDetected;
        }
    }
}

class HAPAccessoryConfigurationLeakSensor
{
    public static function getPosition()
    {
        return 70;
    }

    public static function getCaption()
    {
        return 'Leak Sensor';
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
                'Leak Sensor'           => 'Leckagesensor',
                'VariableID'            => 'VariablenID',
                'Variable missing'      => 'Variable fehlt',
                'Bool required'         => 'Bool benötigt',
                'OK'                    => 'OK'
            ]
        ];
    }
}

HomeKitManager::registerAccessory('LeakSensor');
