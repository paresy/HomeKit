<?php

class HAPAccessoryLeakSensor extends HAPAccessory
{
    public function __construct($data)
    {
        parent::__construct(
            $data,
            [
                new HAPServiceAccessoryInformation(),
                new HAPServiceLeakSensor()
            ]
        );
    }

    public function setCharacteristicIdentify($value)
    {

        //TODO: We probably should send some event
    }

    public function getCharacteristicManufacturer()
    {
        return 'Kai Schnittcher';
    }

    public function getCharacteristicModel()
    {
        return str_replace('HAPAccessory', '', get_class($this));
    }

    public function getCharacteristicName()
    {
        return $this->data['Name'];
    }

    public function getCharacteristicSerialNumber()
    {
        return 'Undefined';
    }

    public function getCharacteristicLeakDetected()
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

        return $value;
    }
}

class HAPAccessoryConfigurationLeakSensor
{
    public static function getPosition()
    {
        return 70;
    }

    public static function getCaption()
    {
        return 'Leak Sensor';
    }

    public static function getColumns()
    {
        return [
            [
                'label' => 'VariableID',
                'name'  => 'VariableID',
                'width' => '100px',
                'add'   => 0,
                'edit'  => [
                    'type' => 'SelectVariable'
                ]
            ]
        ];
    }

    public static function getStatus($data)
    {
        $targetVariable = IPS_GetVariable($data['VariableID']);

        if ($targetVariable['VariableType'] != 1 /* Integer */) {
            return 'Integer required';
        }

        return 'OK';
    }
}

HomeKitManager::registerAccessory('LeakSensor');
