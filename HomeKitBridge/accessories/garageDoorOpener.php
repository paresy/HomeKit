<?php

declare(strict_types=1);

class HAPAccessoryGarageDoorOpener extends HAPAccessoryBase
{
    public function __construct($data)
    {
        parent::__construct(
            $data,
            [
                new HAPServiceAccessoryInformation(),
                new HAPServiceGarageDoorOpener()
            ]
        );
    }

    public function readCharacteristicCurrentDoorState()
    {
        return GetValue($this->data['CurrentDoorState']);
    }

    public function readCharacteristicTargetDoorState()
    {
        return GetValue($this->data['TargetDoorState']);
    }

    public function readCharacteristicObstructionDetected()
    {
        return GetValue($this->data['ObstructionDetected']);
    }

    public function writeCharacteristicCurrentDoorState($value)
    {
        $this->switchDevice($this->data['CurrentDoorState'], $value);
    }

    public function writeCharacteristicTargetDoorState($value)
    {
        $this->switchDevice($this->data['TargetDoorState'], $value);
    }

    public function writeCharacteristicObstructionDetected($value)
    {
        $this->switchDevice($this->data['ObstructionDetected'], $value);
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

class HAPAccessoryConfigurationGarageDoorOpener
{
    public static function getPosition()
    {
        return 80;
    }

    public static function getCaption()
    {
        return 'Garage Door Opener';
    }

    public static function getColumns()
    {
        return [
            [
                'label' => 'CurrentDoorState',
                'name'  => 'CurrentDoorState',
                'width' => '150px',
                'add'   => 0,
                'edit'  => [
                    'type' => 'SelectVariable'
                ],
            ],
            [
                'label' => 'TargetDoorState',
                'name'  => 'TargetDoorState',
                'width' => '150px',
                'add'   => 0,
                'edit'  => [
                    'type' => 'SelectVariable'
                ],
            ],
            [
                'label' => 'ObstructionDetected',
                'name'  => 'ObstructionDetected',
                'width' => '150px',
                'add'   => 0,
                'edit'  => [
                    'type' => 'SelectVariable'
                ],
            ]

        ];
    }

    public static function getStatus($data)
    {
        $variableCurrentDoorState = IPS_GetVariable($data['CurrentDoorState']);
        $variableTargetDoorState = IPS_GetVariable($data['TargetDoorState']);
        $variableObstructionDetected = IPS_GetVariable($data['ObstructionDetected']);

        $error = null;

        if ($variableCurrentDoorState['VariableType'] != 1 /* Integer */) {
            $error .= 'CurrentDorstate: Integer required / ';
        }

        if ($variableTargetDoorState['VariableType'] != 1 /* Integer */) {
            $error .= 'TargetDoorState: Integer required / ';
        }

        if ($variableObstructionDetected['VariableType'] != 0 /* Boolean */) {
            $error .= 'ObstructionDetected: Bool required / ';
        }

        if ($variableTargetDoorState['VariableCustomAction'] != '') {
            $profileAction = $variableTargetDoorState['VariableCustomAction'];
        } else {
            $profileAction = $variableTargetDoorState['VariableAction'];
        }

        if (!($profileAction > 10000)) {
            $error .= 'TargetDoorState: Action required';
        }

        if (!is_null($error)) {
            return $error;
        }

        return 'OK';
    }
}

HomeKitManager::registerAccessory('GarageDoorOpener');
