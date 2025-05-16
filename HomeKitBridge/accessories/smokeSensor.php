<?php

declare(strict_types=1);

class HAPAccessorySmokeSensor extends HAPAccessoryBase
{
    use HelperSwitchDevice;

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
        if (self::getSwitchValue($this->data['VariableID'])) {
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
        return 10;
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
                'Smoke Sensor'         => 'Rauchmelder',
                'VariableID'           => 'VariablenID',
                'Variable missing'     => 'Variable fehlt',
                'Bool required'        => 'Bool benötigt',
                'OK'                   => 'OK'
            ]
        ];
    }
}

HomeKitManager::registerAccessory('SmokeSensor');
