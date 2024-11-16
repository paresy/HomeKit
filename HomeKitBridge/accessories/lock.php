<?php

declare(strict_types=1);

class HAPAccessoryLock extends HAPAccessoryBase
{
    use HelperSwitchDevice;

    public function __construct($data)
    {
        parent::__construct(
            $data,
            [
                new HAPServiceAccessoryInformation(),
                new HAPServiceLock()
            ]
        );
    }

    public function notifyCharacteristicLockCurrentState()
    {
        return [
            $this->data['VariableID']
        ];
    }

    public function readCharacteristicLockCurrentState()
    {
        $targetVariable = IPS_GetVariable($this->data['VariableID']);

        if ($targetVariable['VariableCustomProfile'] != '') {
            $profileName = $targetVariable['VariableCustomProfile'];
        } else {
            $profileName = $targetVariable['VariableProfile'];
        }

        $value = GetValue($this->data['VariableID']);

        //invert value if the variable profile is inverted
        if (strpos($profileName, '.Reversed') !== false) {
            $value = !$value;
        }

        if ($value) {
            return HAPCharacteristicLockCurrentState::Secured;
        }

        //In doubt we return Unsecured
        return HAPCharacteristicLockCurrentState::Unsecured;
    }

    public function notifyCharacteristicLockTargetState()
    {
        return [
            $this->data['VariableID']
        ];
    }

    public function readCharacteristicLockTargetState()
    {
        if (GetValue($this->data['VariableID'])) {
            return HAPCharacteristicLockTargetState::Secured;
        }
        return HAPCharacteristicLockTargetState::Unsecured;
    }

    public function writeCharacteristicLockTargetState($value)
    {
        switch ($value) {
            case HAPCharacteristicLockTargetState::Secured:
                $value = true;
                break;
            case HAPCharacteristicLockTargetState::Unsecured:
                $value = false;
                break;
        }

        self::switchDevice($this->data['VariableID'], $value);
    }
}

class HAPAccessoryConfigurationLock
{
    use HelperSwitchDevice;

    public static function getPosition()
    {
        return 10;
    }

    public static function getCaption()
    {
        return 'Lock';
    }

    public static function getColumns()
    {
        return [
            [
                'label' => 'VariableID',
                'name'  => 'VariableID',
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
        return self::getSwitchCompatibility($data['VariableID']);
    }

    public static function getTranslations()
    {
        return [
            'de' => [
                'Lock'                  => 'Schloss',
                'VariableID'            => 'VariablenID',
                'Variable missing'      => 'Variable fehlt',
                'Bool required'         => 'Bool benötigt',
                'Action required'       => 'Aktion benötigt',
                'OK'                    => 'OK'
            ]
        ];
    }
}

HomeKitManager::registerAccessory('Lock');
