<?php

declare(strict_types=1);

include_once __DIR__ . '/HomeKitBaseTest.php';

class HomeKitThermostatTest extends HomeKitBaseTest
{
    public function testAccessory(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        $thid = IPS_CreateVariable(1 /* Integer */); //TargetHeatingCoolingStateID
        IPS_SetVariableCustomAction($thid, 10001); //Any valid ID will do

        $ctid = IPS_CreateVariable(2 /* Float */); //CurrentTemperatureID

        $ttid = IPS_CreateVariable(2 /* Float */); //TargetTemperatureID
        IPS_SetVariableCustomAction($ttid, 10001); //Any valid ID will do

        IPS_SetProperty($bridgeID, 'AccessoryThermostat', json_encode([
            [
                'ID'                            => 2,
                'Name'                          => 'Test Thermostat',
                'TargetHeatingCoolingStateID'   => $thid,
                'CurrentTemperatureID'          => $ctid,
                'TargetTemperatureID'           => $ttid
            ]
        ]));

        IPS_ApplyChanges($bridgeID);

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        $base = json_decode(file_get_contents(__DIR__ . '/exports/None.json'), true);
        $accessory = json_decode(file_get_contents(__DIR__ . '/exports/Thermostat.json'), true);

        //Check if the generated content matches our test file
        $this->assertEquals(array_merge($base, $accessory), $bridgeInterface->DebugAccessories());
    }

    public function testAccessoryBroken(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        IPS_SetProperty($bridgeID, 'AccessoryThermostat', json_encode([
            [
                'ID'                               => 2,
                'Name'                             => 'Test Thermostat',
                'TargetHeatingCoolingStateID'      => 9999,  /* This is always an invalid variableID */
                'CurrentTemperatureID'             => 9999,  /* This is always an invalid variableID */
                'TargetTemperatureID'              => 9999  /* This is always an invalid variableID */
            ],
        ]));
        IPS_ApplyChanges($bridgeID);

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        //Check if the generated content matches our test file
        $this->assertEquals(json_decode(file_get_contents(__DIR__ . '/exports/None.json'), true), $bridgeInterface->DebugAccessories());
    }
}
