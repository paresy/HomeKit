<?php

declare(strict_types=1);

class HAPAccessoryWindow extends HAPAccessoryBase
{
    public function __construct($data)
    {
        parent::__construct(
            $data,
            [
                new HAPServiceAccessoryInformation(),
                new HAPServiceWindow()
            ]
        );
    }

    public function readCharacteristicTargetPosition()
    {
        return GetValue($this->data['TargetPosition']);
    }

    public function readCharacteristicCurrentPosition()
    {
        return GetValue($this->data['CurrentPosition']);
    }

    public function readCharacteristicPositionState()
    {
        return GetValue($this->data['PositionState']);
    }

    public function writeCharacteristicTargetPosition($value)
    {
        $this->switchDevice($this->data['TargetPosition'], $value);
    }

    public function writeCharacteristicCurrentPosition($value)
    {
        $this->switchDevice($this->data['CurrentPosition'], $value);
    }

    public function writeCharacteristicPositionState($value)
    {
        $this->switchDevice($this->data['PositionState'], $value);
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

class HAPAccessoryConfigurationWindow
{
    public static function getPosition()
    {
        return 90;
    }

    public static function getCaption()
    {
        return 'Window';
    }

    public static function getColumns()
    {
        return [
            [
                'label' => 'TargetPosition',
                'name'  => 'TargetPosition',
                'width' => '150px',
                'add'   => 0,
                'edit'  => [
                    'type' => 'SelectVariable'
                ]
            ],
            [
                'label' => 'CurrentPosition',
                'name'  => 'CurrentPosition',
                'width' => '150px',
                'add'   => 0,
                'edit'  => [
                    'type' => 'SelectVariable'
                ]
            ],
            [
                'label' => 'PositionState',
                'name'  => 'PositionState',
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
        if (!IPS_VariableExists($data['TargetPosition'])) {
            return 'TargetPosition missing';
        }

        if (!IPS_VariableExists($data['CurrentPosition'])) {
            return 'CurrentPosition missing';
        }

        if (!IPS_VariableExists($data['PositionState'])) {
            return 'PositionState missing';
        }

        $variableTargetPosition = IPS_GetVariable($data['TargetPosition']);
        $variableCurrentPosition = IPS_GetVariable($data['CurrentPosition']);
        $variablePositionState = IPS_GetVariable($data['PositionState']);

        if ($variableTargetPosition['VariableType'] != 1 /* Integer */) {
            return 'TargetPosition: Integer required';
        }

        if ($variableCurrentPosition['VariableType'] != 1 /* Integer */) {
            return 'CurrentPosition: Integer required';
        }

        if ($variablePositionState['VariableType'] != 1 /* Integer */) {
            return 'PositionState: Integer required';
        }

        if ($variableTargetPosition['VariableCustomAction'] != '') {
            $profileAction = $variableTargetPosition['VariableCustomAction'];
        } else {
            $profileAction = $variableTargetPosition['VariableAction'];
        }

        if (!($profileAction > 10000)) {
            return 'TargetDoorState: Action required';
        }

        return 'OK';
    }
}

HomeKitManager::registerAccessory('Window');
