<?php

declare(strict_types=1);

class HAPAccessoryContactSensor extends HAPAccessoryBase
{
    use HelperSwitchDevice;

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
        if (self::getSwitchValue($this->data['VariableID'])) {
            return HAPCharacteristicContactSensorState::ContactNotDetected;
        } else {
            return HAPCharacteristicContactSensorState::ContactDetected;
        }
    }
}
class HAPAccessoryConfigurationContactSensor
{
    public static function getPosition()
    {
        return 10;
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

    public static function getObjectIDs($data)
    {
        return [
            $data['VariableID'],
        ];
    }

    public static function getStatus($data)
    {
        if (!IPS_VariableExists($data['VariableID'])) {
            return 'VariableID missing';
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
                'Contact Sensor'       => 'Kontaktsensor',
                'VariableID'           => 'VariablenID',
                'Variable missing'     => 'Variable fehlt',
                'Bool required'        => 'Bool benötigt',
                'OK'                   => 'OK'
            ]
        ];
    }
}
HomeKitManager::registerAccessory('ContactSensor');
