<?php

declare(strict_types=1);

class HAPAccessoryLockMechanism extends HAPAccessoryBase
{
    public function __construct($data)
    {
        parent::__construct(
            $data,
            [
                new HAPServiceAccessoryInformation(),
                new HAPServiceLockMechanism()
            ]
        );
    }

    public function readCharacteristicLockCurrentState()
    {
        return GetValue($this->data['LockCurrentState']);
    }

    public function readCharacteristicLockTargetState()
    {
        return GetValue($this->data['LockTargetState']);
    }

    public function writeCharacteristicLockTargetState($value)
    {
        $this->switchDevice($this->data['LockTargetState'], $value);
    }

    protected function switchDevice($variableID, $value)
    {
        $variableLockTargetState = IPS_GetVariable($variableID);

        if ($variableLockTargetState['VariableCustomAction'] != '') {
            $profileAction = $variableLockTargetState['VariableCustomAction'];
        } else {
            $profileAction = $variableLockTargetState['VariableAction'];
        }

        if ($profileAction < 10000) {
            echo 'No action was defined!';

            return;
        }

        if ($variableLockTargetState['VariableType'] == 0 /* Boolean */) {
            $value = boolval($value);
        } elseif ($variableLockTargetState['VariableType'] == 1 /* Integer */) {
            $value = intval($value);
        } elseif ($variableLockTargetState['VariableType'] == 2 /* Float */) {
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

class HAPAccessoryConfigurationLockMechanism
{
    public static function getPosition()
    {
        return 140;
    }

    public static function getCaption()
    {
        return 'Lock Mechanism';
    }

    public static function getColumns()
    {
        return [
            [
                'label' => 'LockCurrentState',
                'name'  => 'LockCurrentState',
                'width' => '150px',
                'add'   => 0,
                'edit'  => [
                    'type' => 'SelectVariable'
                ]
            ],
            [
                'label' => 'LockTargetState',
                'name'  => 'LockTargetState',
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
        if (!IPS_VariableExists($data['LockCurrentState'])) {
            return 'LockCurrentState missing';
        }

        if (!IPS_VariableExists($data['LockTargetState'])) {
            return 'LockTargetState missing';
        }

        $variableLockCurrentState = IPS_GetVariable($data['LockCurrentState']);
        $variableLockTargetState = IPS_GetVariable($data['LockTargetState']);

        if ($variableLockCurrentState['VariableType'] != 1 /* Integer */) {
            return 'Integer required';
        }

        if ($variableLockTargetState['VariableType'] != 1 /* Integer */) {
            return 'Integer required';
        }

        if ($variableLockTargetState['VariableCustomAction'] != '') {
            $profileAction = $variableLockTargetState['VariableCustomAction'];
        } else {
            $profileAction = $variableLockTargetState['VariableAction'];
        }

        if (!($profileAction > 10000)) {
            return 'Action required';
        }

        return 'OK';
    }
}

HomeKitManager::registerAccessory('LockMechanism');
