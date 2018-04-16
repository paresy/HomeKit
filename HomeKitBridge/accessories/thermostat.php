<?php

declare(strict_types=1);

class HAPAccessoryThermostat extends HAPAccessoryBase
{
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

    public function readCharacteristicCurrentHeatingCoolingState()
    {
        return GetValue($this->data['CurrentHeatingCoolingState']);
    }

    public function readCharacteristicTargetHeatingCoolingState()
    {
        return GetValue($this->data['TargetHeatingCoolingState']);
    }

    public function readCharacteristicCurrentTemperature()
    {
        return GetValue($this->data['CurrentTemperature']);
    }

    public function readCharacteristicTargetTemperature()
    {
        return GetValue($this->data['TargetTemperature']);
    }

    public function readCharacteristicTemperatureDisplayUnits()
    {
        return GetValue($this->data['TemperatureDisplayUnits']);
    }

    public function writeCharacteristicCurrentHeatingCoolingState($value)
    {
        $this->switchDevice($this->data['CurrentHeatingCoolingState'], $value);
    }

    public function writeCharacteristicTargetHeatingCoolingState($value)
    {
        $this->switchDevice($this->data['TargetHeatingCoolingState'], $value);
    }

    public function writeCharacteristicCurrentTemperature($value)
    {
        $this->switchDevice($this->data['CurrentTemperature'], $value);
    }

    public function writeCharacteristicTargetTemperature($value)
    {
        $this->switchDevice($this->data['TargetTemperature'], $value);
    }

    public function writeCharacteristicTemperatureDisplayUnits($value)
    {
        $this->switchDevice($this->data['TemperatureDisplayUnits'], $value);
    }

    protected function switchDevice($variableID, $value)
    {
        $targetVariable = IPS_GetVariable($variableID);

        if ($targetVariable['VariableCustomAction'] != '') {
            $profileAction = $targetVariable['VariableCustomAction'];
        } else {
            $profileAction = $targetVariable['VariableAction'];
        }

        if ($profileAction < 10000) {
            echo 'No action was defined!';

            return;
        }

        if ($targetVariable['VariableType'] == 0 /* Boolean */) {
            $value = boolval($value);
        } elseif ($targetVariable['VariableType'] == 1 /* Integer */) {
            $value = intval($value);
        } elseif ($targetVariable['VariableType'] == 2 /* Float */) {
            $value = floatval($value);
        } else {
            echo 'Strings are not supported';

            return;
        }

        if (IPS_InstanceExists($profileAction)) {
            IPS_RunScriptText('IPS_RequestAction(' . var_export($profileAction, true) . ', ' . var_export(IPS_GetObject($variableID)['ObjectIdent'], true) . ', ' . var_export($value, true) . ');');
        } elseif (IPS_ScriptExists($profileAction)) {
            IPS_RunScriptEx($profileAction, ['VARIABLE' => $variableID, 'VALUE' => $value, 'SENDER' => 'WebFront']);
        }
    }
}

class HAPAccessoryConfigurationThermostat
{
    public static function getPosition()
    {
        return 100;
    }

    public static function getCaption()
    {
        return 'Thermostat';
    }

    public static function getColumns()
    {
        return [
            [
                'label' => 'CurrentHeatingCoolingState',
                'name'  => 'CurrentHeatingCoolingState',
                'width' => '150px',
                'add'   => 0,
                'edit'  => [
                    'type' => 'SelectVariable'
                ]
            ],
            [
                'label' => 'TargetHeatingCoolingState',
                'name'  => 'TargetHeatingCoolingState',
                'width' => '150px',
                'add'   => 0,
                'edit'  => [
                    'type' => 'SelectVariable'
                ]
            ],
            [
                'label' => 'CurrentTemperature',
                'name'  => 'CurrentTemperature',
                'width' => '150px',
                'add'   => 0,
                'edit'  => [
                    'type' => 'SelectVariable'
                ]
            ],
            [
                'label' => 'TargetTemperature',
                'name'  => 'TargetTemperature',
                'width' => '150px',
                'add'   => 0,
                'edit'  => [
                    'type' => 'SelectVariable'
                ]
            ],
            [
                'label' => 'TemperatureDisplayUnits',
                'name'  => 'TemperatureDisplayUnits',
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
        if (!IPS_VariableExists($data['CurrentHeatingCoolingState'])) {
            return 'CurrentHeatingCoolingState missing';
        }

        if (!IPS_VariableExists($data['TargetHeatingCoolingState'])) {
            return 'TargetHeatingCoolingState missing';
        }

        if (!IPS_VariableExists($data['CurrentTemperature'])) {
            return 'CurrentTemperature missing';
        }

        if (!IPS_VariableExists($data['TargetTemperature'])) {
            return 'TargetTemperature missing';
        }

        if (!IPS_VariableExists($data['TemperatureDisplayUnits'])) {
            return 'TemperatureDisplayUnits missing';
        }

        $variableCurrentHeatingCoolingState = IPS_GetVariable($data['CurrentHeatingCoolingState']);
        $variableTargetHeatingCoolingState = IPS_GetVariable($data['TargetHeatingCoolingState']);
        $variableCurrentTemperature = IPS_GetVariable($data['CurrentTemperature']);
        $variableTargetTemperature = IPS_GetVariable($data['TargetTemperature']);
        $variableTemperatureDisplayUnits = IPS_GetVariable($data['TemperatureDisplayUnits']);

        if ($variableCurrentHeatingCoolingState['VariableType'] != 1 /* Integer */) {
            return 'CurrentHeatingCoolingState: Integer required';
        }

        if ($variableTargetHeatingCoolingState['VariableType'] != 1 /* Integer */) {
            return 'TargetHeatingCoolingState: Integer required';
        }

        if ($variableCurrentTemperature['VariableType'] != 2 /* Float */) {
            return 'CurrentTemperature: Float required';
        }

        if ($variableTemperatureDisplayUnits['VariableType'] != 1 /* Integer */) {
            return 'TemperatureDisplayUnits: Integer required';
        }

        if ($variableTargetTemperature['VariableCustomAction'] != '') {
            $profileAction = $variableTargetTemperature['VariableCustomAction'];
        } else {
            $profileAction = $variableTargetTemperature['VariableAction'];
        }

        if (!($profileAction > 10000)) {
            return 'TargetTemperature: Action required';
        }

        if ($variableTargetHeatingCoolingState['VariableCustomAction'] != '') {
            $profileAction = $variableTargetHeatingCoolingState['VariableCustomAction'];
        } else {
            $profileAction = $variableTargetHeatingCoolingState['VariableAction'];
        }

        if (!($profileAction > 10000)) {
            return 'TargetHeatingCoolingState: Action required';
        }

        return 'OK';
    }
}

HomeKitManager::registerAccessory('Thermostat');
