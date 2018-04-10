<?php

declare(strict_types=1);

class HAPAccessoryCarbonDioxideSensor extends HAPAccessoryBase
{
    public function __construct($data)
    {
        parent::__construct(
            $data,
            [
                new HAPServiceAccessoryInformation(),
                new HAPServiceCarbonDioxideSensor()
            ]
        );
    }

    public function readCharacteristicCarbonDioxideDetected()
    {
        return GetValue($this->data['CarbonDioxideDetected']);
    }
}

class HAPAccessoryConfigurationCarbonDioxideSensor
{
    public static function getPosition()
    {
        return 130;
    }

    public static function getCaption()
    {
        return 'Carbon Dioxide Sensor';
    }

    public static function getColumns()
    {
        return [
            [
                'label' => 'CarbonDioxideDetected',
                'name'  => 'CarbonDioxideDetected',
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
        if (!IPS_VariableExists($data['CarbonDioxideDetected'])) {
            return 'CarbonDioxideDetected missing';
        }

        $targetVariable = IPS_GetVariable($data['ContactSensorState']);

        if ($targetVariable['VariableType'] != 1 /* Integer */) {
            return 'Integer required';
        }

        return 'OK';
    }
}

HomeKitManager::registerAccessory('CarbonDioxideSensor');
