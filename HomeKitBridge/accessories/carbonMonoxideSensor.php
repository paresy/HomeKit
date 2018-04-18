<?php

declare(strict_types=1);

class HAPAccessoryCarbonMonoxideSensor extends HAPAccessoryBase
{
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

    public function readCharacteristicCarbonMonoxideDetected()
    {
        return GetValue($this->data['VariableID']);
    }
}

class HAPAccessoryConfigurationCarbonMonoxideSensor
{
    public static function getPosition()
    {
        return 140;
    }

    public static function getCaption()
    {
        return 'Carbon Monoxide Sensor';
    }

    public static function getColumns()
    {
        return [
            [
                'label' => 'CarbonMonoxideDetected',
                'name'  => 'CarbonMonoxideDetected',
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
            return 'VariableID missing';
        }

        $targetVariable = IPS_GetVariable($data['VariableID']);

        if ($targetVariable['VariableType'] != 1 /* Integer */) {
            return 'Integer required';
        }

        return 'OK';
    }
}

HomeKitManager::registerAccessory('CarbonMonoxideSensor');
