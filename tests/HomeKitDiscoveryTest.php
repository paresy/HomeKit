<?php

declare(strict_types=1);

include_once __DIR__ . '/stubs/GlobalStubs.php';
include_once __DIR__ . '/stubs/KernelStubs.php';
include_once __DIR__ . '/stubs/ModuleStubs.php';

use PHPUnit\Framework\TestCase;

class HomeKitDiscoveryTest extends TestCase
{
    private $bridgeModuleID = '{7FC71134-CFD0-4909-819C-B794FE067FBC}';
    private $discoveryModuleID = '{69D234C2-A453-4399-B766-71FB7D663700}';
    private $multicastModuleID = '{BAB408E0-0A0F-48C3-B14E-9FB2FA81F66A}';

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
        $iid = IPS_CreateInstance($this->discoveryModuleID);
        $this->assertEquals(count(IPS_GetInstanceListByModuleID($this->discoveryModuleID)), 1);
        $this->assertEquals(count(IPS_GetInstanceListByModuleID($this->multicastModuleID)), 1);
        $this->assertEquals(IPS_GetInstance($iid)['ConnectionID'], IPS_GetInstanceListByModuleID($this->multicastModuleID)[0]);
    }

    public function testConfigurationForm(): void
    {
        $iid = IPS_CreateInstance($this->discoveryModuleID);
        $form = json_decode(IPS_GetConfigurationForParent($iid), true);

        $this->assertEquals($form, [
            'Host'               => '224.0.0.251',
            'Port'               => 5353,
            'BindPort'           => 5353,
            'MulticastIP'        => '224.0.0.251',
            'EnableBroadcast'    => true,
            'EnableReuseAddress' => true,
            'EnableLoopback'     => true
        ]);
    }

    public function testAnnounce(): void
    {
        $discoveryID = IPS_CreateInstance($this->discoveryModuleID);
        $discoveryInterface = IPS\InstanceManager::getInstanceInterface($discoveryID);
        $multicastID = IPS_GetInstance($discoveryID)['ConnectionID'];
        $multicastInterface = IPS\InstanceManager::getInstanceInterface($multicastID);
        IPS_SetProperty($multicastID, 'BindIP', '0.0.0.0');
        IPS_ApplyChanges($multicastID);

        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        //Associate Bridge with Discovery
        IPS_SetProperty($bridgeID, 'DiscoveryInstanceID', $discoveryID);
        IPS_ApplyChanges($bridgeID);

        //This should send something through the multicast socket
        $discoveryInterface->AnnounceBridge();

        //Make just a simple check if our multicast socket received anything
        $this->assertTrue($multicastInterface->HasText());
    }
}
