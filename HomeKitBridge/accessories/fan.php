<?php

declare(strict_types=1);

class HAPAccessoryFan extends HAPAccessoryBase
{
    use HelperDimDevice;

    public function __construct($data)
    {
        parent::__construct(
            $data,
            [
                new HAPServiceAccessoryInformation(),
                new HAPServiceFan()
            ]
        );
    }

    public function notifyCharacteristicOn()
    {
        return [
            $this->data['VariableID']
        ];
    }

    public function readCharacteristicOn()
    {
        return self::getDimValue($this->data['VariableID']) > 0;
    }

    public function writeCharacteristicOn($value)
    {
        //Only switch the device on, if it isn't on.
        //This should fix the problem that Apple sends on before dimming
        if ($value && $this->readCharacteristicOn()) {
            return;
        }

        if ($value) {
            self::dimDevice($this->data['VariableID'], 100);
        } else {
            self::dimDevice($this->data['VariableID'], 0);
        }
    }

    public function notifyCharacteristicRotationSpeed()
    {
        return [
            $this->data['VariableID']
        ];
    }

    public function readCharacteristicRotationSpeed()
    {
        return self::getDimValue($this->data['VariableID']);
    }

    public function writeCharacteristicRotationSpeed($value)
    {
        self::dimDevice($this->data['VariableID'], $value);
    }
}

class HAPAccessoryConfigurationFan
{
    use HelperDimDevice;

    public static function getPosition()
    {
        return 10;
    }

    public static function getCaption()
    {
        return 'Fan';
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
                'Fan'                   => 'Lüfter',
                'VariableID'            => 'VariablenID',
                'Variable missing'      => 'Variable fehlt',
                'Int/Float required'    => 'Int/Float benötigt',
                'Profile required'      => 'Profil benötigt',
                'Action required'       => 'Aktion benötigt',
                'OK'                    => 'OK'
            ]
        ];
    }
}

HomeKitManager::registerAccessory('Fan');
