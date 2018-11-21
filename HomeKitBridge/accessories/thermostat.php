<?php

declare(strict_types=1);

class HAPAccessoryThermostat extends HAPAccessoryBase
{
    use HelperSetDevice;

    public function __construct($data)
    {
        parent::__construct(
            $data,
            [
                new HAPServiceAccessoryInformation(),
                new HAPServiceThermostat()
            ]
        );
    }

    public function notifyCharacteristicCurrentHeatingCoolingState()
    {
        return [
            $this->data['CurrentHeatingCoolingStateID']
        ];
    }

    public function notifyCharacteristicTargetHeatingCoolingState()
    {
        return [
            $this->data['TargetHeatingCoolingStateID']
        ];
    }

    public function notifyCharacteristicCurrentTemperature()
    {
        return [
            $this->data['CurrentTemperatureID']
        ];
    }

    public function notifyCharacteristicTemperatureDisplayUnits()
    {
        return [
            HAPCharacteristicTemperatureDisplayUnits::Celsius
        ];
    }

    public function readCharacteristicCurrentHeatingCoolingState()
    {
        return GetValue($this->data['CurrentHeatingCoolingStateID']);
    }

    public function readCharacteristicTargetHeatingCoolingState()
    {
        return GetValue($this->data['TargetHeatingCoolingStateID']);
    }

    public function readCharacteristicCurrentTemperature()
    {
        return GetValue($this->data['CurrentTemperatureID']);
    }

    public function readCharacteristicTemperatureDisplayUnits()
    {
        return HAPCharacteristicTemperatureDisplayUnits::Celsius;
    }

    public function writeCharacteristicTargetHeatingCoolingState($value)
    {
        self::setDevice($this->data['TargetHeatingCoolingStateID'], $value);
    }

    public function writeCharacteristicTemperatureDisplayUnits($value)
    {
        return;
    }
}

class HAPAccessoryConfigurationThermostat
{
    use HelperSetDevice;

    public static function getPosition()
    {
        return 260;
    }

    public static function getCaption()
    {
        return 'Thermostat';
    }

    public static function getColumns()
    {
        return [
            [
                'label' => 'CurrentHeatingCoolingStateID',
                'name'  => 'CurrentHeatingCoolingStateID',
                'width' => '250px',
                'add'   => 0,
                'edit'  => [
                    'type' => 'SelectVariable'
                ]
            ],
            [
                'label' => 'TargetHeatingCoolingStateID',
                'name'  => 'TargetHeatingCoolingStateID',
                'width' => '250px',
                'add'   => 0,
                'edit'  => [
                    'type' => 'SelectVariable'
                ]
            ],
            [
                'label' => 'CurrentTemperatureID',
                'name'  => 'CurrentTemperatureID',
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
        if (!IPS_VariableExists($data['CurrentHeatingCoolingStateID'])) {
            return 'Variable CurrentHeatingCoolingStateID missing';
        }

        if (!IPS_VariableExists($data['TargetHeatingCoolingStateID'])) {
            return 'Variable TargetHeatingCoolingStateID missing';
        }

        if (!IPS_VariableExists($data['CurrentTemperatureID'])) {
            return 'Variable CurrentTemperatureID missing';
        }

        $targetVariable = IPS_GetVariable($data['TargetHeatingCoolingStateID']);

        if ($targetVariable['VariableType'] != 1 /* Integer */ ) {
            return 'TargetHeatingCoolingStateID: Int required';
        }

        if ($targetVariable['VariableCustomAction'] != '') {
            $profileAction = $targetVariable['VariableCustomAction'];
        } else {
            $profileAction = $targetVariable['VariableAction'];
        }

        if (!($profileAction > 10000)) {
            return 'TargetHeatingCoolingStateID: Action required';
        }

        $targetVariable = IPS_GetVariable($data['CurrentHeatingCoolingStateID']);

        if ($targetVariable['VariableType'] != 1 /* Integer */ ) {
            return 'CurrentHeatingCoolingStateID: Int required';
        }

        $targetVariable = IPS_GetVariable($data['CurrentTemperatureID']);

        if ($targetVariable['VariableType'] != 2 /* Float */ ) {
            return 'CurrentTemperatureID: Float required';
        }

        return 'OK';
    }

    public static function getTranslations()
    {
        return [
            'de' => [
                'Thermostat'               => 'Thermostat',
                'CurrentHeatingCoolingStateID'                => 'Aktueller Heizungsstatus (ID)',
                'TargetHeatingCoolingStateID'              => 'Ziel Heizungsstatus (ID)',
                'CurrentTemperatureID'      => 'Aktuelle Temperatur (ID) ',
                'Variable CurrentHeatingCoolingStateID missing'         => 'Variable CurrentHeatingCoolingStateID fehlt',
                'Variable CurrentTemperatureID missing'       => 'Variable CurrentTemperatureID fehlt',
                'TargetHeatingCoolingStateID: Int required' => 'TargetHeatingCoolingStateID: Int benötigt',
                'TargetHeatingCoolingStateID: Action required' => 'TargetHeatingCoolingStateID: Aktion benötigt',
                'CurrentHeatingCoolingStateID: Int required' => 'CurrentHeatingCoolingStateID: Int benötigt',
                'CurrentTemperatureID: Float required' => 'CurrentTemperatureID: Float benötigt',
                'OK'                    => 'OK'
            ]
        ];
    }
}

HomeKitManager::registerAccessory('Thermostat');
