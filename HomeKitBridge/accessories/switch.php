<?php

declare(strict_types=1);

class HAPAccessorySwitch extends HAPAccessoryBase
{
    use HelperSwitchDevice;

    public function __construct($data)
    {
        parent::__construct(
            $data,
            [
                new HAPServiceAccessoryInformation(),
                new HAPServiceSwitch()
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
        return GetValue($this->data['VariableID']);
    }

    public function writeCharacteristicOn($value)
    {
        self::switchDevice($this->data['VariableID'], $value);
    }
}

class HAPAccessoryConfigurationSwitch
{
    use HelperSwitchDevice;

    public static function getPosition()
    {
        return 40;
    }

    public static function getCaption()
    {
        return 'Switch';
    }

    public static function getColumns()
    {
        return [
            [
                'label' => 'VariableID',
                'name'  => 'VariableID',
                'width' => '150px',
                'add'   => 0,
                'edit'  => [
                    'type' => 'SelectVariable'
                ]
            ]
        ];
    }

    public static function getStatus($data)
    {
        return self::getSwitchCompatibility($data['VariableID']);
    }

    public static function getTranslations()
    {
        return [
            'de' => [
                'Switch'                => 'Zwischenstecker',
                'VariableID'            => 'VariablenID',
                'Variable missing'      => 'Variable fehlt',
                'Bool required'         => 'Bool benötigt',
                'Action required'       => 'Aktion benötigt',
                'OK'                    => 'OK'
            ]
        ];
    }
}

HomeKitManager::registerAccessory('Switch');
