<?php

declare(strict_types=1);
class HAPAccessoryLightbulbSwitch extends HAPAccessory
{
    public function __construct($data)
    {
        parent::__construct(
            $data,
            [
                new HAPServiceAccessoryInformation(),
                new HAPServiceLightbulb()
            ]
        );
    }

    public function setCharacteristicIdentify($value)
    {

        //TODO: We probably should send some event
    }

    public function getCharacteristicManufacturer()
    {
        return 'Symcon GmbH';
    }

    public function getCharacteristicModel()
    {
        return str_replace('HAPAccessory', '', get_class($this));
    }

    public function getCharacteristicName()
    {
        return $this->data['Name'];
    }

    public function getCharacteristicSerialNumber()
    {
        return 'Undefined';
    }

    public function getCharacteristicOn()
    {
        return GetValue($this->data['VariableID']);
    }

    public function setCharacteristicOn($value)
    {
        $this->switchDevice($this->data['VariableID'], $value);
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

class HAPAccessoryConfigurationLightbulbSwitch
{
    public static function getPosition()
    {
        return 1;
    }

    public static function getCaption()
    {
        return 'Lightbulb (Switch)';
    }

    public static function getColumns()
    {
        return [
            [
                'label' => 'VariableID',
                'name'  => 'VariableID',
                'width' => '100px',
                'add'   => 0,
                'edit'  => [
                    'type' => 'SelectVariable'
                ]
            ]
        ];
    }

    public static function getStatus($data)
    {
        $targetVariable = IPS_GetVariable($data['VariableID']);

        if ($targetVariable['VariableType'] != 0 /* Boolean */) {
            return 'Bool required';
        }

        if ($targetVariable['VariableCustomAction'] != '') {
            $profileAction = $targetVariable['VariableCustomAction'];
        } else {
            $profileAction = $targetVariable['VariableAction'];
        }

        if (!($profileAction > 10000)) {
            return 'Action required';
        }

        return 'OK';
    }
}

HomeKitManager::registerAccessory('LightbulbSwitch');
