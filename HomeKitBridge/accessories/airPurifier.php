<?php

declare(strict_types=1);

class HAPAccessoryAirPurifier extends HAPAccessoryBase
{
    public function __construct($data)
    {
        parent::__construct(
            $data,
            [
                new HAPServiceAccessoryInformation(),
                new HAPServiceAirPurifier()
            ]
        );
    }

    public function readCharacteristicActive()
    {
        return GetValue($this->data['Active']);
    }

    public function writeCharacteristicActive($value)
    {
        $this->switchDevice($this->data['Active'], $value);
    }

    public function readCharacteristicCurrentAirPurifierState()
    {
        return GetValue($this->data['CurrentAirPurifierState']);
    }

    public function readCharacteristicTargetAirPurifierState()
    {
        return GetValue($this->data['TargetAirPurifierState']);
    }

    public function writeCharacteristicTargetAirPurifierState($value)
    {
        $this->switchDevice($this->data['TargetAirPurifierState'], $value);
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

class HAPAccessoryConfigurationAirPurifier
{
    public static function getPosition()
    {
        return 150;
    }

    public static function getCaption()
    {
        return 'Air Purifier';
    }

    public static function getColumns()
    {
        return [
            [
                'label' => 'Active',
                'name'  => 'Active',
                'width' => '150px',
                'add'   => 0,
                'edit'  => [
                    'type' => 'SelectVariable'
                ],
            ],
            [
                'label' => 'CurrentAirPurifierState',
                'name'  => 'CurrentAirPurifierState',
                'width' => '150px',
                'add'   => 0,
                'edit'  => [
                    'type' => 'SelectVariable'
                ],
            ],
            [
                'label' => 'TargetAirPurifierState',
                'name'  => 'TargetAirPurifierState',
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
        if (!IPS_VariableExists($data['Active'])) {
            return 'Variable Active missing';
        }

        if (!IPS_VariableExists($data['CurrentAirPurifierState'])) {
            return 'Variable CurrentAirPurifierState missing';
        }

        if (!IPS_VariableExists($data['TargetAirPurifierState'])) {
            return 'Variable TargetAirPurifierState missing';
        }

        $activeVariable = IPS_GetVariable($data['Active']);
        $TargetAirPurifierStateVariable = IPS_GetVariable($data['TargetAirPurifierState']);

        if ($activeVariable['VariableType'] != 1 /* Integer */) {
            return 'Int required';
        }

        if ($TargetAirPurifierStateVariable['VariableType'] != 1 /* Integer */) {
            return 'Int required';
        }

        return 'OK';
    }
}

HomeKitManager::registerAccessory('AirPurifier');
