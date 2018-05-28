<?php

declare(strict_types=1);

class HAPAccessoryLockMechanism extends HAPAccessoryBase
{
    use HelperSetDevice;

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

    public function notifyCharacteristicLockCurrentState()
    {
        return [
            $this->data['VariableID']
        ];
    }

    public function readCharacteristicLockCurrentState()
    {
        return GetValue($this->data['VariableID']);
		
    }

    public function notifyCharacteristicLockTargetState()
    {
        return [
            $this->data['VariableID']
        ];
    }

    public function readCharacteristicLockTargetState()
    {
        return GetValue($this->data['VariableID']);
    }

    public function writeCharacteristicLockTargetState($value)
    {
        self::setDevice($this->data['VariableID'], $value);
    }
}

class HAPAccessoryConfigurationLockMechanism
{
    use HelperSetDevice;
	
    public static function getPosition()
    {
        return 91;
    }

    public static function getCaption()
    {
        return 'Lock Mechanism';
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
            $data['VariableID'] = $data['LockTargetState'];
            unset($data['LockCurrentState']);
            unset($data['LockTargetState']);
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
	    
	if ($targetVariable['VariableCustomProfile'] != '') {
            $profileName = $targetVariable['VariableCustomProfile'];
        } else {
            $profileName = $targetVariable['VariableProfile'];
        }
        if (!IPS_VariableProfileExists($profileName)) {
            return 'Profile required';
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
                'Lock Mechanism'    => 'Schloss',
                'VariableID'            => 'VariablenID',
                'Variable missing'      => 'Variable fehlt',
                'Int required'          => 'Int benötigt',
                'Profile required'      => 'Profil benötigt',
		'Action required'	=> 'Aktionsscript benötigt'
                'OK'                    => 'OK'
            ]
        ];
    }
}

HomeKitManager::registerAccessory('LockMechanism');
