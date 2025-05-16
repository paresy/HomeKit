<?php

declare(strict_types=1);

class HAPAccessoryCarbonMonoxideSensor extends HAPAccessoryBase
{
    use HelperSwitchDevice;

    public function __construct($data)
    {
        parent::__construct(
            $data,
            [
                new HAPServiceAccessoryInformation(),
                new HAPServiceCarbonMonoxideSensor()
            ]
        );
    }

    public function notifyCharacteristicCarbonMonoxideDetected()
    {
        return [
            $this->data['VariableID']
        ];
    }

    public function readCharacteristicCarbonMonoxideDetected()
    {
        if (self::getSwitchValue($this->data['VariableID'])) {
            return HAPCharacteristicCarbonMonoxideDetected::Abnormal;
        } else {
            return HAPCharacteristicCarbonMonoxideDetected::Normal;
        }
    }
}

class HAPAccessoryConfigurationCarbonMonoxideSensor
{
    public static function getPosition()
    {
        return 10;
    }

    public static function getCaption()
    {
        return 'Carbon Monoxide Sensor';
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
                'Carbon Monoxide Sensor'    => 'Kohlenmonoxid Sensor',
                'VariableID'                => 'VariablenID',
                'Variable missing'          => 'Variable fehlt',
                'Bool required'             => 'Bool benötigt',
                'OK'                        => 'OK'
            ]
        ];
    }
}

HomeKitManager::registerAccessory('CarbonMonoxideSensor');
