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
        $this->assertEquals(count(IPS_GetInstanceListByModuleID($this->bridgeModuleID)), 1);
        $this->assertEquals(count(IPS_GetInstanceListByModuleID($this->serverModuleID)), 1);
        $this->assertEquals(IPS_GetInstance($iid)['ConnectionID'], IPS_GetInstanceListByModuleID($this->serverModuleID)[0]);
    }

    public function testConfigurationForm(): void
    {
        $iid = IPS_CreateInstance($this->bridgeModuleID);
        $form = json_decode(IPS_GetConfigurationForParent($iid), true);

        $this->assertEquals($form, [
            'Port' => IPS_GetProperty($iid, "BridgePort")
        ]);
    }

    public function testAccessories(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        //Check if the generated content matches our test file
        $this->assertEquals(json_decode($bridgeInterface->DebugAccessories()), json_decode(file_get_contents(__DIR__ . '/Accessories/None.json')));
    }
}
