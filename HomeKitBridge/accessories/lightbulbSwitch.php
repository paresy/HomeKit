<?php

declare(strict_types=1);

class HAPAccessoryLightbulbSwitch extends HAPAccessoryBase
{
    use HelperSwitchDevice;

    public function __construct($data)
    {
        parent::__construct(
            $data,
            [
                new HAPServiceAccessoryInformation(),
                new HAPServiceLightbulb()
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

class HAPAccessoryConfigurationLightbulbSwitch
{
    use HelperSwitchDevice;

    public static function getPosition()
    {
        return 1;
    }

    public static function getCaption()
    {
        return 'Lightbulb (Switch)';
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
            "de" => [
                "Lightbulb (Switch)"    => "Lampe (Schaltbar)",
                "VariableID"            => "VariablenID",
                "Variable missing"      => "Variable fehlt",
                "Bool required"         => "Bool benötigt",
                "Action required"       => "Aktion benötigt",
                "OK"                    => "OK"
            ]
        ];
    }
}

HomeKitManager::registerAccessory('LightbulbSwitch');
