<?php

declare(strict_types=1);

class HAPAccessoryAirQualitySensor extends HAPAccessoryBase
{
    public function __construct($data)
    {
        parent::__construct(
            $data,
            [
                new HAPServiceAccessoryInformation(),
                new HAPServiceAirQualitySensor()
            ]
        );
    }

    public function notifyCharacteristicAirQuality()
    {
        return [
            $this->data['VariableID']
        ];
    }

    public function readCharacteristicAirQuality()
    {
        return GetValue($this->data['VariableID']);
    }
}

class HAPAccessoryConfigurationAirQualitySensor
{
    public static function getPosition()
    {
        return 70;
    }

    public static function getCaption()
    {
        return 'Air Quality Sensor';
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

        if ($targetVariable['VariableType'] != 1 /* Integer */ && $targetVariable['VariableType'] != 2 /* Float */) {
            return 'Int/Float required';
        }

        return 'OK';
    }

    public static function getTranslations()
    {
        return [
            'de' => [
                'Air Quality Sensor'    => 'Luftgütesensor',
                'VariableID'            => 'VariablenID',
                'Variable missing'      => 'Variable fehlt',
                'Int/Float required'    => 'Int/Float benötigt',
                'OK'                    => 'OK'
            ]
        ];
    }
}

HomeKitManager::registerAccessory('AirQualitySensor');
