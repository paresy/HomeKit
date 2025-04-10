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

        if ($targetVariable['VariableType'] != VARIABLETYPE_INTEGER) {
            return 'Int required';
        }

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

        if (!HasAction($data['VariableID'])) {
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