<?php

declare(strict_types=1);

class HAPAccessoryMotionSensor extends HAPAccessoryBase
{
    use HelperSwitchDevice;

    public function __construct($data)
    {
        parent::__construct(
            $data,
            [
                new HAPServiceAccessoryInformation(),
                new HAPServiceMotionSensor()
            ]
        );
    }

    public function notifyCharacteristicMotionDetected()
    {
        return [
            $this->data['VariableID']
        ];
    }

    public function readCharacteristicMotionDetected()
    {
        return self::GetSwitchValue($this->data['VariableID']);
    }
}

class HAPAccessoryConfigurationMotionSensor
{
    public static function getPosition()
    {
        return 10;
    }

    public static function getCaption()
    {
        return 'Motion Sensor';
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
                'Motion Sensor'         => 'Bewegungsmelder',
                'VariableID'            => 'VariablenID',
                'Variable missing'      => 'Variable fehlt',
                'Bool required'         => 'Bool benötigt',
                'OK'                    => 'OK'
            ]
        ];
    }
}

HomeKitManager::registerAccessory('MotionSensor');
