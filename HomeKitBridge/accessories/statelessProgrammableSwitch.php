<?php

declare(strict_types=1);

class HAPAccessoryStatelessProgrammableSwitch extends HAPAccessoryBase
{
    public function __construct($data)
    {
        parent::__construct(
            $data,
            [
                new HAPServiceAccessoryInformation(),
                new HAPServiceStatelessProgrammableSwitch()
            ]
        );
    }

    public function notifyCharacteristicProgrammableSwitchEvent()
    {
        return [
            $this->data['VariableID']
        ];
    }

    public function readCharacteristicProgrammableSwitchEvent()
    {
        return GetValue($this->data['VariableID']);
    }
}

class HAPAccessoryConfigurationStatelessProgrammableSwitch
{
    use HelperSetDevice;

    public static function getPosition()
    {
        return 300;
    }

    public static function getCaption()
    {
        return 'Stateless Programmable Switch';
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
        if (!IPS_VariableExists($data['VariableID'])) {
            return 'Variable missing';
        }

        $targetVariable = IPS_GetVariable($data['VariableID']);

        if ($targetVariable['VariableType'] != 1 /* Integer */) {
            return 'Int required';
        }
        return 'OK';
    }

    public static function getTranslations()
    {
        return [
            'de' => [
                'Stateless Programmable Switch'                => 'Zustandsloser programmierbarer Schalter',
                'VariableID'                                   => 'VariablenID',
                'Variable missing'                             => 'Variable fehlt',
                'Int required'                                 => 'Int benötigt',
                'OK'                                           => 'OK'
            ]
        ];
    }
}

HomeKitManager::registerAccessory('StatelessProgrammableSwitch');
