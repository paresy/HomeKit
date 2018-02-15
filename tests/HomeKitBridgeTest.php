<?php

declare(strict_types=1);

include_once __DIR__ . '/stubs/GlobalStubs.php';
include_once __DIR__ . '/stubs/MessageStubs.php';
include_once __DIR__ . '/stubs/KernelStubs.php';
include_once __DIR__ . '/stubs/ModuleStubs.php';

use PHPUnit\Framework\TestCase;

class HomeKitBridgeTest extends TestCase
{
    private $bridgeModuleID = '{7FC71134-CFD0-4909-819C-B794FE067FBC}';
    private $serverModuleID = '{8062CF2B-600E-41D6-AD4B-1BA66C32D6ED}';

    public function setUp()
    {
        //Reset
        IPS\Kernel::reset();

        //Register our i/o stubs for testing
        IPS\ModuleLoader::loadLibrary(__DIR__ . '/stubs/IOStubs/library.json');

        //Register our library we need for testing
        IPS\ModuleLoader::loadLibrary(__DIR__ . '/../library.json');

        parent::setUp();
    }

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

}
