<?php

declare(strict_types=1);

class HAPAccessoryWindowCoveringUpDown extends HAPAccessoryBase
{
    use HelperSetDevice;

    public function __construct($data)
    {
        parent::__construct(
            $data,
            [
                new HAPServiceAccessoryInformation(),
                new HAPServiceWindowCovering()
            ]
        );
    }

    public function notifyCharacteristicTargetPosition()
    {
        return [
            $this->data['VariableID']
        ];
    }

    public function readCharacteristicTargetPosition()
    {
        return $this->readCharacteristicCurrentPosition();
    }

    public function writeCharacteristicTargetPosition($value)
    {
        if ($value > 0) {
            $this->setDevice($this->data['VariableID'], 0 /* Open */);
        } else {
            $this->setDevice($this->data['VariableID'], 4 /* Close */);
        }
    }

    public function notifyCharacteristicCurrentPosition()
    {
        return [
            $this->data['VariableID']
        ];
    }

    public function readCharacteristicCurrentPosition()
    {
        switch (GetValue($this->data['VariableID'])) {
            case 0: /* Open */
                return 100;
            case 2: /* Stop */
                return 50;
            case 4: /* Close */
                return 0;
        }

        return 50; /* Undefined. Return something... */
    }

    public function notifyCharacteristicPositionState()
    {
        return [];
    }

    public function readCharacteristicPositionState()
    {
        return HAPCharacteristicPositionState::Stopped;
    }

    public function writeCharacteristicHoldPosition($value)
    {
        if ($value) {
            $this->setDevice($this->data['VariableID'], 2 /* Stop */);
        }
    }
}

class HAPAccessoryConfigurationWindowCoveringUpDown
{
    public static function getPosition()
    {
        return 120;
    }

    public static function getCaption()
    {
        return 'Window Covering (Up/Down)';
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
            case '~ShutterMoveStop':
            case '~ShutterMoveStep':
                break;
            default:
                return 'Unsupported Profile';
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

    public static function getTranslations()
    {
        return [
            'de' => [
                'Window Covering (Up/Down)'      => 'Rolladen/Jalousie (Hoch/Runter)',
                'VariableID'                     => 'VariablenID',
                'Variable missing'               => 'Variable fehlt',
                'Int required'                   => 'Int benötigt',
                'Profile required'               => 'Profil benötigt',
                'Unsupported Profile'            => 'Falsches Profil',
                'OK'                             => 'OK'
            ]
        ];
    }
}

HomeKitManager::registerAccessory('WindowCoveringUpDown');
