<?php

declare(strict_types=1);

class HAPAccessoryHumiditySensor extends HAPAccessoryBase
{
    public function __construct($data)
    {
        parent::__construct(
            $data,
            [
                new HAPServiceAccessoryInformation(),
                new HAPServiceHumiditySensor()
            ]
        );
    }

    public function notifyCharacteristicCurrentRelativeHumidity()
    {
        return [
            $this->data['VariableID']
        ];
    }

    public function readCharacteristicCurrentRelativeHumidity()
    {
        return GetValue($this->data['VariableID']);
    }
}

class HAPAccessoryConfigurationHumiditySensor
{
    public static function getPosition()
    {
        return 20;
    }

    public static function getCaption()
    {
        return 'Humidity Sensor';
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
                'Humidity Sensor'       => 'Feuchtigkeitssensor',
                'VariableID'            => 'VariablenID',
                'Variable missing'      => 'Variable fehlt',
                'Int/Float required'    => 'Int/Float benötigt',
                'OK'                    => 'OK'
            ]
        ];
    }
}

HomeKitManager::registerAccessory('HumiditySensor');
