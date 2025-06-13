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
        return [
            $this->data['VariableID'],
            $this->data['AlarmID']
        ];
    }

    public function readCharacteristicSecuritySystemCurrentState()
    {
        if (IPS_VariableExists($this->data['AlarmID']) && GetValue($this->data['AlarmID'])) {
            return HAPCharacteristicSecuritySystemCurrentState::AlarmTriggered;
        }
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
            ],
            [
                'label' => 'AlarmID (optional)',
                'name'  => 'AlarmID',
                'width' => '250px',
                'add'   => 0,
                'edit'  => [
                    'type' => 'SelectVariable'
                ]
            ]
        ];
    }

    public static function getObjectIDs($data)
    {
        return [
            $data['VariableID'],
            $data['AlarmID'],
        ];
    }

    public static function getStatus($data)
    {
        if (!IPS_VariableExists($data['VariableID'])) {
            return 'Variable missing';
        }

        $targetVariable = IPS_GetVariable($data['VariableID']);

        if ($targetVariable['VariableType'] != VARIABLETYPE_INTEGER) {
            return 'Int required';
        }

        if (!HasAction($data['VariableID'])) {
            return 'Action required';
        }

        if (!function_exists('IPS_GetVariablePresentation')) {
            $profileName = '';
            if ($targetVariable['VariableCustomProfile'] != '') {
                $profileName = $targetVariable['VariableCustomProfile'];
            } else {
                $profileName = $targetVariable['VariableProfile'];
            }
            if ($profileName != 'SecuritySystem.HomeKit') {
                return 'Unsupported Profile';
            }
        } else {
            $presentation = IPS_GetVariablePresentation($data['VariableID']);
            switch ($presentation['PRESENTATION'] ?? 'Invalid Presentation') {
                case VARIABLE_PRESENTATION_LEGACY:
                    if ($presentation['PROFILE'] != 'SecuritySystem.HomeKit') {
                        return 'Unsupported Profile';
                    }
                    // No break. Add additional comment above this line if intentional
                case VARIABLE_PRESENTATION_ENUMERATION:
                    break;
                default:
                    return 'Unsupported Presentation';
            }
        }

        return 'OK';
    }

    public static function getTranslations()
    {
        return [
            'de' => [
                'Security System'     => 'Sicherheitssystem',
                'VariableID'          => 'VariablenID',
                'AlarmID (optional)'  => 'AlarmID (Optional)',
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