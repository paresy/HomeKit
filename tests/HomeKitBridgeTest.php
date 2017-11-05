<?php

declare(strict_types=1);

include_once __DIR__ . '/SymconGlobalStubs.php';
include_once __DIR__ . '/SymconKernelStubs.php';
include_once __DIR__ . '/SymconModuleStubs.php';

use PHPUnit\Framework\TestCase;

class HomeKitBridgeTest extends TestCase
{
    private $bridgeModuleID = '{7FC71134-CFD0-4909-819C-B794FE067FBC}';
    private $serverModuleID = '{8062CF2B-600E-41D6-AD4B-1BA66C32D6ED}';
    private $discoveryModuleID = '{69D234C2-A453-4399-B766-71FB7D663700}';

    public function setUp()
    {
        //Reset
        IPS\Kernel::reset();

        //Register our i/o stubs for testing
        IPS\Kernel::loadLibrary(__DIR__ . '/SymconIOStubs/library.json');

        //Register our library we need for testing
        IPS\Kernel::loadLibrary(__DIR__ . '/../library.json');

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
            'Open' => false,
            'Port' => 0
        ]);
    }

    public function testAccessories(): void
    {
        $discoveryID = IPS_CreateInstance($this->discoveryModuleID);
        $multicastID = IPS_GetInstance($discoveryID)['ConnectionID'];
        IPS_SetProperty($multicastID, 'BindIP', '0.0.0.0');
        IPS_ApplyChanges($multicastID);

        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        //Associate Bridge with Discovery
        IPS_SetProperty($bridgeID, 'DiscoveryInstanceID', $discoveryID);
        IPS_ApplyChanges($bridgeID);

        $bridgeInterface = IPS\Kernel::getInstanceInterface($bridgeID);

        //Check if the generated content matches our test file
        $this->assertEquals(json_decode($bridgeInterface->DebugAccessories()), json_decode(file_get_contents(__DIR__ . '/Accessories/None.json')));
    }
}
