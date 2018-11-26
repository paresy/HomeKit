<?php

declare(strict_types=1);

class HAPAccessoryLightSensor extends HAPAccessoryBase
{
    public function __construct($data)
    {
        parent::__construct(
            $data,
            [
                new HAPServiceAccessoryInformation(),
                new HAPServiceLightSensor()
            ]
        );
    }

    public function notifyCharacteristicCurrentAmbientLightLevel()
    {
        return [
            $this->data['VariableID']
        ];
    }

    public function readCharacteristicCurrentAmbientLightLevel()
    {
        return GetValue($this->data['VariableID']);
    }
}

class HAPAccessoryConfigurationLightSensor
{
    public static function getPosition()
    {
        return 10;
    }

    public static function getCaption()
    {
        return 'Light Sensor';
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

        if ($targetVariable['VariableType'] != 1 /* Integer */ && $targetVariable['VariableType'] != 2 /* Float */) {
            return 'Int/Float required';
        }

        return 'OK';
    }

    public static function getTranslations()
    {
        return [
            'de' => [
                'Light Sensor'          => 'Helligkeitssensor',
                'VariableID'            => 'VariablenID',
                'Variable missing'      => 'Variable fehlt',
                'Int/Float required'    => 'Int/Float benötigt',
                'OK'                    => 'OK'
            ]
        ];
    }
}

HomeKitManager::registerAccessory('LightSensor');
