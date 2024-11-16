<?php

declare(strict_types=1);

class HAPAccessorySecuritySystem extends HAPAccessoryBase
{
    use HelperSetDevice;

    public function __construct($data)
    {
        parent::__construct(
            $data,
            [
                new HAPServiceAccessoryInformation(),
                new HAPServiceSecuritySystem()
            ]
        );
    }

    public function notifyCharacteristicSecuritySystemCurrentState()
    {
        return $this->notifyCharacteristicSecuritySystemTargetState();
    }

    public function readCharacteristicSecuritySystemCurrentState()
    {
        return $this->readCharacteristicSecuritySystemTargetState();
    }

    public function notifyCharacteristicSecuritySystemTargetState()
    {
        return [
            $this->data['VariableID']
        ];
    }

    public function readCharacteristicSecuritySystemTargetState()
    {
        return GetValue($this->data['VariableID']);
    }

    public function writeCharacteristicSecuritySystemTargetState($value)
    {
        $this->setDevice($this->data['VariableID'], $value);
    }
}

class HAPAccessoryConfigurationSecuritySystem
{
    public static function getPosition()
    {
        return 100;
    }

    public static function getCaption()
    {
        return 'Security System';
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
        if (!IPS_VariableExists($data['VariableID'])) {
            return 'Variable missing';
        }

        $targetVariable = IPS_GetVariable($data['VariableID']);

        if ($targetVariable['VariableType'] != 1 /* Integer */) {
            return 'Int required';
        }

        if ($targetVariable['VariableCustomProfile'] != '') {
            $profileName = $targetVariable['VariableCustomProfile'];
        } else {
            $profileName = $targetVariable['VariableProfile'];
        }

        if (!IPS_VariableProfileExists($profileName)) {
            return 'Profile required';
        }

        switch ($profileName) {
            case 'SecuritySystem.HomeKit':
                break;
            default:
                return 'Unsupported Profile';
        }

        if ($targetVariable['VariableCustomAction'] != 0) {
            $profileAction = $targetVariable['VariableCustomAction'];
        } else {
            $profileAction = $targetVariable['VariableAction'];
        }

        if (!($profileAction > 10000)) {
            return 'Action required';
        }

        return 'OK';
    }

    public static function getTranslations()
    {
        return [
            'de' => [
                'Security System'     => 'Sicherheitssystem',
                'VariableID'          => 'VariablenID',
                'Variable missing'    => 'Variable fehlt',
                'Int required'        => 'Int benötigt',
                'Profile required'    => 'Profil benötigt',
                'Unsupported Profile' => 'Falsches Profil',
                'OK'                  => 'OK',
            ]
        ];
    }
}

HomeKitManager::registerAccessory('SecuritySystem');