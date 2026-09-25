<?php

declare(strict_types=1);

class HAPAccessoryGarageDoorOpener extends HAPAccessoryBase
{
    use HelperShutterDevice;

    public function __construct($data)
    {
        parent::__construct(
            $data,
            [
                new HAPServiceAccessoryInformation(),
                new HAPServiceGarageDoorOpener()
            ]
        );
    }

    public function notifyCharacteristicCurrentDoorState()
    {
        return [
            $this->data['VariableID']
        ];
    }

    public function readCharacteristicCurrentDoorState()
    {
        switch (GetValue($this->data['VariableID'])) {
            case 0:
                return HAPCharacteristicCurrentDoorState::Open;
            case 2:
                return HAPCharacteristicCurrentDoorState::Stopped;
            case 4:
                return HAPCharacteristicCurrentDoorState::Closed;
        }
        //In doubt we return Stopped
        return HAPCharacteristicCurrentDoorState::Stopped;
    }

    public function notifyCharacteristicTargetDoorState()
    {
        return [
            $this->data['VariableID']
        ];
    }

    public function readCharacteristicTargetDoorState()
    {
        switch (GetValue($this->data['VariableID'])) {
            case 0:
                return HAPCharacteristicTargetDoorState::Open;
            case 4:
                return HAPCharacteristicTargetDoorState::Closed;
        }
        return HAPCharacteristicTargetDoorState::Closed;
    }

    public function writeCharacteristicTargetDoorState($value)
    {
        self::setShutterOpen($this->data['VariableID'], $value == HAPCharacteristicTargetDoorState::Open);
    }

    public function notifyCharacteristicObstructionDetected()
    {
        return [];
    }

    public function readCharacteristicObstructionDetected()
    {
        return false;
    }
}

class HAPAccessoryConfigurationGarageDoorOpener
{
    use HelperShutterDevice;

    public static function getPosition()
    {
        return 10;
    }

    public static function getCaption()
    {
        return 'Garage Door Opener';
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
            $data['VariableID'] = $data['TargetDoorState'];
            unset($data['CurrentDoorState']);
            unset($data['TargetDoorState']);
            unset($data['ObstructionDetected']);
            return true;
        }
        return false;
    }

    public static function getObjectIDs($data)
    {
        return [
            $data['VariableID'],
        ];
    }

    public static function getStatus($data)
    {
        return self::getShutterCompatibility($data['VariableID']);
    }

    public static function getTranslations()
    {
        return [
            'de' => [
                'Garage Door Opener'    => 'Garagentor',
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

HomeKitManager::registerAccessory('GarageDoorOpener');
