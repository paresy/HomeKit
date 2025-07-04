<?php

declare(strict_types=1);

class HAPAccessoryWindowCoveringPosition extends HAPAccessoryBase
{
    use HelperDimDevice;

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
        $this->dimDevice($this->data['VariableID'], 100 - $value);
    }

    public function notifyCharacteristicCurrentPosition()
    {
        return [
            $this->data['VariableID']
        ];
    }

    public function readCharacteristicCurrentPosition()
    {
        return 100 - $this->getDimValue($this->data['VariableID']);
    }

    public function notifyCharacteristicPositionState()
    {
        return [];
    }

    public function readCharacteristicPositionState()
    {
        return HAPCharacteristicPositionState::Stopped;
    }
}

class HAPAccessoryConfigurationWindowCoveringPosition
{
    use HelperDimDevice;

    public static function getPosition()
    {
        return 10;
    }

    public static function getCaption()
    {
        return 'Window Covering (Position)';
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

    public static function getObjectIDs($data)
    {
        return [
            $data['VariableID'],
        ];
    }

    public static function getStatus($data)
    {
        return self::getDimCompatibility($data['VariableID']);
    }

    public static function getTranslations()
    {
        return [
            'de' => [
                'Window Covering (Position)'     => 'Rollladen/Jalousie (Position)',
                'VariableID'                     => 'VariablenID',
                'Variable missing'               => 'Variable fehlt',
                'Int/Float required'             => 'Int/Float benötigt',
                'Profile required'               => 'Profil benötigt',
                'Action required'                => 'Aktion benötigt',
                'OK'                             => 'OK'
            ]
        ];
    }
}

HomeKitManager::registerAccessory('WindowCoveringPosition');
