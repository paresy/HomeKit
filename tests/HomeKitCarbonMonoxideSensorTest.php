<?php

declare(strict_types=1);

include_once __DIR__ . '/HomeKitBaseTest.php';

class HomeKitCarbonMonoxideSensorTest extends HomeKitBaseTest
{
    public function testAccessoryCarbonMonoxideSensor(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        $vid = IPS_CreateVariable(1 /* Integer */);

        IPS_SetProperty($bridgeID, 'AccessoryCarbonMonoxideSensor', json_encode([
            [
                'ID'         => 3,
                'Name'       => 'Test',
                'VariableID' => $vid
            ]
        ]));
        IPS_ApplyChanges($bridgeID);

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        $base = json_decode(file_get_contents(__DIR__ . '/Accessories/None.json'), true);
        $carbonMonoxideSensor = json_decode(file_get_contents(__DIR__ . '/Accessories/CarbonMonoxideSensor.json'), true);

        //Check if the generated content matches our test file
        $this->assertEquals(array_merge($base, $carbonMonoxideSensor), $bridgeInterface->DebugAccessories());
    }

    public function testAccessoryCarbonMonoxideSensorInvalidValue(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        $vid = IPS_CreateVariable(1 /* Integer */);

        SetValue($vid, -5);

        IPS_SetProperty($bridgeID, 'AccessoryCarbonMonoxideSensor', json_encode([
            [
                'ID'         => 3,
                'Name'       => 'Test',
                'VariableID' => $vid
            ]
        ]));
        IPS_ApplyChanges($bridgeID);

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        $base = json_decode(file_get_contents(__DIR__ . '/Accessories/None.json'), true);
        $carbonMonoxideSensor = json_decode(file_get_contents(__DIR__ . '/Accessories/CarbonMonoxideSensor.json'), true);

        //Check if the generated content matches our test file
        $this->assertEquals(array_merge($base, $carbonMonoxideSensor), $bridgeInterface->DebugAccessories());
    }

    public function testAccessoryCarbonMonoxideSensorBroken(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        IPS_SetProperty($bridgeID, 'AccessoryCarbonMonoxideSensor', json_encode([
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
