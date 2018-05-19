<?php

declare(strict_types=1);

class HAPAccessoryGarageDoorOpener extends HAPAccessoryBase
{
    use HelperSwitchDevice;

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
            $this->data['CurrentDoorState']
        ];
    }

    public function readCharacteristicCurrentDoorState()
    {
        return GetValue($this->data['CurrentDoorState']);
    }

    public function notifyCharacteristicTargetDoorState()
    {
        return [
            $this->data['TargetDoorState']
        ];
    }

    public function readCharacteristicTargetDoorState()
    {
        return GetValue($this->data['TargetDoorState']);
    }

    public function writeCharacteristicTargetDoorState($value)
    {
        self::switchDevice($this->data['TargetDoorState'], $value);
    }

    public function notifyCharacteristicObstructionDetected()
    {
        return [
            $this->data['ObstructionDetected']
        ];
    }

    public function readCharacteristicObstructionDetected()
    {
        return GetValue($this->data['ObstructionDetected']);
    }
}

class HAPAccessoryConfigurationGarageDoorOpener
{
    public static function getPosition()
    {
        return 80;
    }

    public static function getCaption()
    {
        return 'Garage Door Opener';
    }

    public static function getColumns()
    {
        return [
            [
                'label' => 'CurrentDoorState',
                'name'  => 'CurrentDoorState',
                'width' => '150px',
                'add'   => 0,
                'edit'  => [
                    'type' => 'SelectVariable'
                ],
            ],
            [
                'label' => 'TargetDoorState',
                'name'  => 'TargetDoorState',
                'width' => '150px',
                'add'   => 0,
                'edit'  => [
                    'type' => 'SelectVariable'
                ],
            ],
            [
                'label' => 'ObstructionDetected',
                'name'  => 'ObstructionDetected',
                'width' => '150px',
                'add'   => 0,
                'edit'  => [
                    'type' => 'SelectVariable'
                ],
            ]

        ];
    }

    public static function getStatus($data)
    {
        if (!IPS_VariableExists($data['CurrentDoorState'])) {
            return 'CurrentDoorState missing';
        }

        if (!IPS_VariableExists($data['TargetDoorState'])) {
            return 'TargetDoorState missing';
        }

        if (!IPS_VariableExists($data['ObstructionDetected'])) {
            return 'ObstructionDetected missing';
        }

        $variableCurrentDoorState = IPS_GetVariable($data['CurrentDoorState']);
        $variableTargetDoorState = IPS_GetVariable($data['TargetDoorState']);
        $variableObstructionDetected = IPS_GetVariable($data['ObstructionDetected']);

        if ($variableCurrentDoorState['VariableType'] != 1 /* Integer */) {
            return 'CurrentDoorState: Integer required';
        }

        if ($variableTargetDoorState['VariableType'] != 1 /* Integer */) {
            return 'TargetDoorState: Integer required';
        }

        if ($variableObstructionDetected['VariableType'] != 0 /* Boolean */) {
            return 'ObstructionDetected: Bool required';
        }

        if ($variableTargetDoorState['VariableCustomAction'] != '') {
            $profileAction = $variableTargetDoorState['VariableCustomAction'];
        } else {
            $profileAction = $variableTargetDoorState['VariableAction'];
        }

        if (!($profileAction > 10000)) {
            return 'TargetDoorState: Action required';
        }

        return 'OK';
    }
}

HomeKitManager::registerAccessory('GarageDoorOpener');
