<?php

declare(strict_types=1);

class HAPAccessoryLeakSensor extends HAPAccessoryBase
{
    use HelperSwitchDevice;

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
        if (self::getSwitchValue($this->data['VariableID'])) {
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
        return 10;
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
