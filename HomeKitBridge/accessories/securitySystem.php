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
            $this->data['VariableID']
        ];
    }

    public function readCharacteristicSecuritySystemCurrentState()
    {
        return GetValue($this->data['VariableID']);
		
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
        self::setDevice($this->data['VariableID'], $value);
    }
}

class HAPAccessoryConfigurationSecuritySystem
{
	use HelperSetDevice;
	
    public static function getPosition()
    {
        return 99;
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

    public static function doMigrate(&$data)
    {
        if (!isset($data['VariableID'])) {
            $data['VariableID'] = $data['SecuritySystemTargetState'];
            unset($data['SecuritySystemCurrentState']);
            unset($data['SecuritySystemTargetState']);
            return true;
        }
        return false;
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
                'Security System'    => 'Alarmanlage',
                'VariableID'            => 'VariablenID',
                'Variable missing'      => 'Variable fehlt',
                'Int required'          => 'Int benötigt',
                'Profile required'      => 'Profil benötigt',
                'Unsupported Profile'   => 'Falsches Profil',
                'OK'                    => 'OK'
            ]
        ];
    }
}

HomeKitManager::registerAccessory('SecuritySystem');
