<?php

declare(strict_types=1);

include_once __DIR__ . '/HomeKitBaseTest.php';

class HomeKitCarbonDioxideSensorTest extends HomeKitBaseTest
{
    public function testAccessoryCarbonDioxideSensor(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        $vid = IPS_CreateVariable(1 /* Integer */);

        IPS_SetProperty($bridgeID, 'AccessoryCarbonDioxideSensor', json_encode([
            [
                'ID'         => 3,
                'Name'       => 'Test',
                'VariableID' => $vid
            ]
        ]));
        IPS_ApplyChanges($bridgeID);

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        $base = json_decode(file_get_contents(__DIR__ . '/Accessories/None.json'), true);
        $carbonDioxideSensorSensor = json_decode(file_get_contents(__DIR__ . '/Accessories/CarbonDioxideSensor.json'), true);

        //Check if the generated content matches our test file
        $this->assertEquals(array_merge($base, $carbonDioxideSensorSensor), $bridgeInterface->DebugAccessories());
    }

    public function testAccessoryCarbonDioxideSensorInvalidValue(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        $vid = IPS_CreateVariable(1 /* Integer */);

        SetValue($vid, -5);

        IPS_SetProperty($bridgeID, 'AccessoryCarbonDioxideSensor', json_encode([
            [
                'ID'         => 3,
                'Name'       => 'Test',
                'VariableID' => $vid
            ]
        ]));
        IPS_ApplyChanges($bridgeID);

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        $base = json_decode(file_get_contents(__DIR__ . '/Accessories/None.json'), true);
        $carbonDioxideSensorSensor = json_decode(file_get_contents(__DIR__ . '/Accessories/CarbonDioxideSensor.json'), true);

        //Check if the generated content matches our test file
        $this->assertEquals(array_merge($base, $carbonDioxideSensorSensor), $bridgeInterface->DebugAccessories());
    }

    public function testAccessoryCarbonDioxideSensorBroken(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        IPS_SetProperty($bridgeID, 'AccessoryCarbonDioxideSensor', json_encode([
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
