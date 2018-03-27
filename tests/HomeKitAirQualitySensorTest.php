<?php

declare(strict_types=1);

include_once __DIR__ . '/HomeKitBaseTest.php';

class HomeKitAirQualitySensorTest extends HomeKitBaseTest
{
    public function testAccessoryAirQualitySensor(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        $vid = IPS_CreateVariable(1 /* Integer */);

        IPS_SetProperty($bridgeID, 'AccessoryAirQualitySensor', json_encode([
            [
                'ID'         => 3,
                'Name'       => 'Test',
                'VariableID' => $vid
            ]
        ]));
        IPS_ApplyChanges($bridgeID);

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        $base = json_decode(file_get_contents(__DIR__ . '/Accessories/None.json'), true);
        $airQualitySensor = json_decode(file_get_contents(__DIR__ . '/Accessories/AirQualitySensor.json'), true);

        //Check if the generated content matches our test file
        $this->assertEquals(array_merge($base, $airQualitySensor), $bridgeInterface->DebugAccessories());
    }

    public function testAccessoryAirQualitySensorInvalidValue(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        $vid = IPS_CreateVariable(1 /* Integer */);

        SetValue($vid, -5);

        IPS_SetProperty($bridgeID, 'AccessoryAirQualitySensor', json_encode([
            [
                'ID'         => 3,
                'Name'       => 'Test',
                'VariableID' => $vid
            ]
        ]));
        IPS_ApplyChanges($bridgeID);

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        $base = json_decode(file_get_contents(__DIR__ . '/Accessories/None.json'), true);
        $airQualitySensor = json_decode(file_get_contents(__DIR__ . '/Accessories/AirQualitySensor.json'), true);

        //Check if the generated content matches our test file
        $this->assertEquals(array_merge($base, $airQualitySensor), $bridgeInterface->DebugAccessories());
    }

    public function testAccessoryAirQualitySensorBroken(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        IPS_SetProperty($bridgeID, 'AccessoryAirQualitySensor', json_encode([
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
