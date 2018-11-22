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
        return [];
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

    public function notifyCharacteristicTargetTemperature()
    {
        return [
            $this->data['TargetTemperatureID']
        ];
    }

    public function notifyCharacteristicTemperatureDisplayUnits()
    {
        return [];
    }

    public function readCharacteristicCurrentHeatingCoolingState()
    {
        If(GetValue(($this->data['TargetHeatingCoolingStateID'])) == HAPCharacteristicTargetHeatingCoolingState::Off) {
            return HAPCharacteristicCurrentHeatingCoolingState::Off;
        }

        If(GetValue(($this->data['CurrentTemperatureID'])) < GetValue($this->data['TargetTemperatureID'])) {
            return HAPCharacteristicCurrentHeatingCoolingState::Heat;
        }
        elseif (GetValue(($this->data['TargetHeatingCoolingStateID'])) == HAPCharacteristicTargetHeatingCoolingState::Cool) {
            return HAPCharacteristicCurrentHeatingCoolingState::Cool;
        }
        return HAPCharacteristicCurrentHeatingCoolingState::Off;
    }

    public function readCharacteristicTargetHeatingCoolingState()
    {
        return GetValue($this->data['TargetHeatingCoolingStateID']);
    }

    public function readCharacteristicCurrentTemperature()
    {
        return GetValue(($this->data['CurrentTemperatureID']));
    }

    public function readCharacteristicTargetTemperature()
    {
        return GetValue($this->data['TargetTemperatureID']);
    }

    public function readCharacteristicTemperatureDisplayUnits()
    {
        return HAPCharacteristicTemperatureDisplayUnits::Celsius;
    }

    public function writeCharacteristicTargetHeatingCoolingState($value)
    {
        //Gibt noch kein Helper für setDevice ohne Profil
        $variableID = $this->data['TargetHeatingCoolingStateID'];
        if (!IPS_VariableExists($variableID)) {
            return false;
        }
        $targetVariable = IPS_GetVariable($variableID);

        if ($targetVariable['VariableCustomAction'] != 0) {
            $profileAction = $targetVariable['VariableCustomAction'];
        } else {
            $profileAction = $targetVariable['VariableAction'];
        }

        if ($profileAction < 10000) {
            return false;
        }

        if (IPS_InstanceExists($profileAction)) {
            IPS_RunScriptText('IPS_RequestAction(' . var_export($profileAction, true) . ', ' . var_export(IPS_GetObject($variableID)['ObjectIdent'], true) . ', ' . var_export($value, true) . ');');
        } elseif (IPS_ScriptExists($profileAction)) {
            IPS_RunScriptEx($profileAction, ['VARIABLE' => $variableID, 'VALUE' => $value, 'SENDER' => 'VoiceControl']);
        } else {
            return false;
        }

        return true;
    }

    public function writeCharacteristicTargetTemperature($value)
    {
        //Hier wird TargetTemperature von HomeKit in IPS geschrieben
        //IPS_LogMessage("Test", $value);
        self::setDevice($this->data['TargetTemperatureID'], floatval($value));
    }

    public function writeCharacteristicTemperatureDisplayUnits($value)
    {

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
            ],
            [
                'label' => 'TargetTemperatureID',
                'name'  => 'TargetTemperatureID',
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

        if ($targetVariable['VariableType'] != 1 /* Integer */) {
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

        if ($targetVariable['VariableType'] != 1 /* Integer */) {
            return 'CurrentHeatingCoolingStateID: Int required';
        }

        $targetVariable = IPS_GetVariable($data['CurrentTemperatureID']);

        if ($targetVariable['VariableType'] != 2 /* Float */) {
            return 'CurrentTemperatureID: Float required';
        }

        $targetVariable = IPS_GetVariable($data['TargetTemperatureID']);

        if ($targetVariable['VariableType'] != 2 /* Integer */) {
            return 'TargetTemperatureID: Float required';
        }

        if ($targetVariable['VariableCustomAction'] != '') {
            $profileAction = $targetVariable['VariableCustomAction'];
        } else {
            $profileAction = $targetVariable['VariableAction'];
        }

        if (!($profileAction > 10000)) {
            return 'TargetTemperatureID: Action required';
        }

        return 'OK';
    }

    public static function getTranslations()
    {
        return [
            'de' => [
                'Thermostat'                                            => 'Thermostat',
                'CurrentHeatingCoolingStateID'                          => 'Aktueller Heizungsstatus (ID)',
                'TargetHeatingCoolingStateID'                           => 'Ziel Heizungsstatus (ID)',
                'CurrentTemperatureID'                                  => 'Aktuelle Temperatur (ID) ',
                'Variable CurrentHeatingCoolingStateID missing'         => 'Variable CurrentHeatingCoolingStateID fehlt',
                'Variable CurrentTemperatureID missing'                 => 'Variable CurrentTemperatureID fehlt',
                'TargetHeatingCoolingStateID: Int required'             => 'TargetHeatingCoolingStateID: Int benötigt',
                'TargetHeatingCoolingStateID: Action required'          => 'TargetHeatingCoolingStateID: Aktion benötigt',
                'TargetTemperatureID: Action required'                  => 'TargetTemperatureID: Aktion benötigt',
                'CurrentHeatingCoolingStateID: Int required'            => 'CurrentHeatingCoolingStateID: Int benötigt',
                'CurrentTemperatureID: Float required'                  => 'CurrentTemperatureID: Float benötigt',
                'TargetTemperatureID: Float required'                   => 'TargetTemperatureID: Float benötigt',
                'OK'                                                    => 'OK'
            ]
        ];
    }
}

HomeKitManager::registerAccessory('Thermostat');
