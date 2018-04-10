<?php

declare(strict_types=1);

class HAPAccessoryContactSensor extends HAPAccessoryBase
{
    public function __construct($data)
    {
        parent::__construct(
            $data,
            [
                new HAPServiceAccessoryInformation(),
                new HAPServiceContactSensor()
            ]
        );
    }

    public function readCharacteristicContactSensorState()
    {
        return GetValue($this->data['ContactSensorState']);
    }
}

class HAPAccessoryConfigurationContactSensor
{
    public static function getPosition()
    {
        return 110;
    }

    public static function getCaption()
    {
        return 'Contact Sensor';
    }

    public static function getColumns()
    {
        return [
            [
                'label' => 'ContactSensorState',
                'name'  => 'ContactSensorState',
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
        if (!IPS_VariableExists($data['ContactSensorState'])) {
            return 'ContactSensorState missing';
        }

        $targetVariable = IPS_GetVariable($data['ContactSensorState']);

        if ($targetVariable['VariableType'] != 1 /* Integer */) {
            return 'Integer required';
        }

        return 'OK';
    }
}

HomeKitManager::registerAccessory('ContactSensor');
