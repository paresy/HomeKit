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

    public function notifyCharacteristicContactSensorState()
    {
        return [
            $this->data['VariableID']
        ];
    }

    public function readCharacteristicContactSensorState()
    {
        switch (GetValue($this->data['VariableID'])) {
            case 0:
                return HAPCharacteristicContactSensorState::ContactNotDetected;
            case 1:
                return HAPCharacteristicContactSensorState::ContactDetected;
        }
        return HAPCharacteristicContactSensorState::ContactDetected;
    }
}

class HAPAccessoryConfigurationContactSensor
{
    public static function getPosition()
    {
        return 111;
    }

    public static function getCaption()
    {
        return 'Contact Sensor';
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

//        if ($targetVariable['VariableType'] != 1 /* Integer */ && $targetVariable['VariableType'] != 2 /* Float */) {
//            return 'Int/Float required';
//        }

        return 'OK';
    }

    public static function getTranslations()
    {
        return [
            'de' => [
                'Contact Sensor'    => 'Fensterkontakt',
                'VariableID'            => 'VariablenID',
                'Variable missing'      => 'Variable fehlt',
                'Int/Float required'    => 'Int/Float benötigt',
                'OK'                    => 'OK'
            ]
        ];
    }
}

HomeKitManager::registerAccessory('ContactSensor');
