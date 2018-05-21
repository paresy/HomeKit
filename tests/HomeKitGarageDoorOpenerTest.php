<?php

declare(strict_types=1);

include_once __DIR__ . '/HomeKitBaseTest.php';

class HomeKitGarageDoorOpenerTest extends HomeKitBaseTest
{
    public function testAccessory(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        $VariableID = IPS_CreateVariable(1 /* Integer */);

        //Currently stubs do not provide default profiles
        if (!IPS_VariableProfileExists('~ShutterMoveStop')) {
            IPS_CreateVariableProfile('~ShutterMoveStop', 1 /* Integer */);
        }

        IPS_SetVariableCustomProfile($VariableID, '~ShutterMoveStop');
        IPS_SetVariableCustomAction($VariableID, 10001); //Any valid ID will do

        IPS_SetProperty($bridgeID, 'AccessoryGarageDoorOpener', json_encode([
            [
                'ID'                    => 3,
                'Name'                  => 'Test',
                'VariableID'            => $VariableID
            ]
        ]));
        IPS_ApplyChanges($bridgeID);

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        $base = json_decode(file_get_contents(__DIR__ . '/exports/None.json'), true);
        $accessory = json_decode(file_get_contents(__DIR__ . '/exports/GarageDoorOpener.json'), true);

        //Check if the generated content matches our test file
        $this->assertEquals(array_merge($base, $accessory), $bridgeInterface->DebugAccessories());
    }

    public function testAccessoryWithMigrate(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        $CurrentDoorStateID = IPS_CreateVariable(1 /* Integer */);
        $TargetDoorStateID = IPS_CreateVariable(1 /* Integer */);
        $ObstructionDetectedID = IPS_CreateVariable(0 /* Boolean */);

        //Currently stubs do not provide default profiles
        if (!IPS_VariableProfileExists('~ShutterMoveStop')) {
            IPS_CreateVariableProfile('~ShutterMoveStop', 1 /* Integer */);
        }

        IPS_SetVariableCustomProfile($TargetDoorStateID, '~ShutterMoveStop');
        IPS_SetVariableCustomAction($TargetDoorStateID, 10001); //Any valid ID will do

        IPS_SetProperty($bridgeID, 'AccessoryGarageDoorOpener', json_encode([
            [
                'ID'                    => 3,
                'Name'                  => 'Test',
                'CurrentDoorState'      => $CurrentDoorStateID,
                'TargetDoorState'       => $TargetDoorStateID,
                'ObstructionDetected'   => $ObstructionDetectedID
            ]
        ]));
        IPS_ApplyChanges($bridgeID);

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        $base = json_decode(file_get_contents(__DIR__ . '/exports/None.json'), true);
        $accessory = json_decode(file_get_contents(__DIR__ . '/exports/GarageDoorOpener.json'), true);

        //Check if the generated content matches our test file
        $this->assertEquals(array_merge($base, $accessory), $bridgeInterface->DebugAccessories());
    }
}
