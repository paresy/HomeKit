<?php

declare(strict_types=1);

include_once __DIR__ . '/HomeKitBaseTest.php';

class HomeKitLightbulbSwitchTest extends HomeKitBaseTest
{
    public function testCreate(): void
    {
        $iid = IPS_CreateInstance($this->bridgeModuleID);
        $this->assertEquals(1, count(IPS_GetInstanceListByModuleID($this->bridgeModuleID)));
        $this->assertEquals(1, count(IPS_GetInstanceListByModuleID($this->serverModuleID)));
        $this->assertEquals(IPS_GetInstanceListByModuleID($this->serverModuleID)[0], IPS_GetInstance($iid)['ConnectionID']);
    }

    public function testConfigurationForm(): void
    {
        $iid = IPS_CreateInstance($this->bridgeModuleID);
        $form = json_decode(IPS_GetConfigurationForParent($iid), true);

        $this->assertEquals([
            'Port' => IPS_GetProperty($iid, 'BridgePort')
        ], $form);
    }

    public function testAccessories(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        //Check if the generated content matches our test file
        $this->assertEquals(json_decode(file_get_contents(__DIR__ . '/Accessories/None.json'), true), $bridgeInterface->DebugAccessories());
    }

    public function testAccessoryLightbulbSwitch(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        $vid = IPS_CreateVariable(0 /* Boolean */);

        IPS_SetVariableCustomAction($vid, 10001); //Any valid ID will do

        IPS_SetProperty($bridgeID, 'AccessoryLightbulbSwitch', json_encode([
            [
                'ID'         => 2,
                'Name'       => 'Test',
                'VariableID' => $vid
            ]
        ]));
        IPS_ApplyChanges($bridgeID);

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        $base = json_decode(file_get_contents(__DIR__ . '/Accessories/None.json'), true);
        $lightbulbSwitch = json_decode(file_get_contents(__DIR__ . '/Accessories/LightbulbSwitch.json'), true);

        //Check if the generated content matches our test file
        $this->assertEquals(array_merge($base, $lightbulbSwitch), $bridgeInterface->DebugAccessories());
    }

    public function testAccessoryLightbulbSwitchBroken(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        $vid = IPS_CreateVariable(0 /* Boolean */);

        IPS_SetProperty($bridgeID, 'AccessoryLightbulbSwitch', json_encode([
            [
                'ID'         => 2,
                'Name'       => 'Test',
                'VariableID' => $vid /* The action is missing */
            ]
        ]));
        IPS_ApplyChanges($bridgeID);

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        //Check if the generated content matches our test file
        $this->assertEquals(json_decode(file_get_contents(__DIR__ . '/Accessories/None.json'), true), $bridgeInterface->DebugAccessories());
    }

    public function testAccessoryLightSensor(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        $vid = IPS_CreateVariable(2 /* Float */);

        IPS_SetProperty($bridgeID, 'AccessoryLightSensor', json_encode([
            [
                'ID'         => 3,
                'Name'       => 'Test',
                'VariableID' => $vid
            ]
        ]));
        IPS_ApplyChanges($bridgeID);

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        $base = json_decode(file_get_contents(__DIR__ . '/Accessories/None.json'), true);
        $lightSensor = json_decode(file_get_contents(__DIR__ . '/Accessories/LightSensor.json'), true);

        //Check if the generated content matches our test file
        $this->assertEquals(array_merge($base, $lightSensor), $bridgeInterface->DebugAccessories());
    }

    public function testAccessoryLightSensorInvalidValue(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        $vid = IPS_CreateVariable(2 /* Float */);

        SetValue($vid, -5);

        IPS_SetProperty($bridgeID, 'AccessoryLightSensor', json_encode([
            [
                'ID'         => 3,
                'Name'       => 'Test',
                'VariableID' => $vid
            ]
        ]));
        IPS_ApplyChanges($bridgeID);

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        $base = json_decode(file_get_contents(__DIR__ . '/Accessories/None.json'), true);
        $lightSensor = json_decode(file_get_contents(__DIR__ . '/Accessories/LightSensor.json'), true);

        //Check if the generated content matches our test file
        $this->assertEquals(array_merge($base, $lightSensor), $bridgeInterface->DebugAccessories());
    }

    public function testAccessoryLightSensorBroken(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        IPS_SetProperty($bridgeID, 'AccessoryLightSensor', json_encode([
            [
                'ID'         => 3,
                'Name'       => 'Test',
                'VariableID' => 9999 /* This is always an invalid variableID */
            ]
        ]));
        IPS_ApplyChanges($bridgeID);

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        //Check if the generated content matches our test file
        $this->assertEquals(json_decode(file_get_contents(__DIR__ . '/Accessories/None.json'), true), $bridgeInterface->DebugAccessories());
    }

    public function testAccessoryTemperatureSensor(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        $vid = IPS_CreateVariable(2 /* Float */);

        IPS_SetProperty($bridgeID, 'AccessoryTemperatureSensor', json_encode([
            [
                'ID'         => 3,
                'Name'       => 'Test',
                'VariableID' => $vid
            ]
        ]));
        IPS_ApplyChanges($bridgeID);

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        $base = json_decode(file_get_contents(__DIR__ . '/Accessories/None.json'), true);
        $temperatureSensor = json_decode(file_get_contents(__DIR__ . '/Accessories/TemperatureSensor.json'), true);

        //Check if the generated content matches our test file
        $this->assertEquals(array_merge($base, $temperatureSensor), $bridgeInterface->DebugAccessories());
    }

    public function testAccessoryTemperatureSensorInvalidValue(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        $vid = IPS_CreateVariable(2 /* Float */);

        SetValue($vid, -5);

        IPS_SetProperty($bridgeID, 'AccessoryTemperatureSensor', json_encode([
            [
                'ID'         => 3,
                'Name'       => 'Test',
                'VariableID' => $vid
            ]
        ]));
        IPS_ApplyChanges($bridgeID);

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        $base = json_decode(file_get_contents(__DIR__ . '/Accessories/None.json'), true);
        $temperatureSensor = json_decode(file_get_contents(__DIR__ . '/Accessories/TemperatureSensor.json'), true);

        //Check if the generated content matches our test file
        $this->assertEquals(array_merge($base, $temperatureSensor), $bridgeInterface->DebugAccessories());
    }
}
