<?php

declare(strict_types=1);

include_once __DIR__ . '/HomeKitBaseTest.php';

class HomeKitLightSensorTest extends HomeKitBaseTest
{

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

}
