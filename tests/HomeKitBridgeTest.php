<?php

declare(strict_types=1);

include_once __DIR__ . '/HomeKitBaseTest.php';

class HomeKitBridgeTest extends HomeKitBaseTest
{
    public function testCreate(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);
        $this->assertEquals(1, count(IPS_GetInstanceListByModuleID($this->bridgeModuleID)));

        //The parent is no longer created by the module, but described via GetCompatibleParents
        $this->assertEquals(0, count(IPS_GetInstanceListByModuleID($this->serverModuleID)));
        $this->assertEquals([
            'type'      => 'require',
            'moduleIDs' => [$this->serverModuleID]
        ], json_decode(IPS\InstanceManager::getInstanceInterface($bridgeID)->GetCompatibleParents(), true));
    }

    public function testSetupCodeWithoutParent(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        ob_start();
        $setupCode = $bridgeInterface->RestartPairing();
        $output = ob_get_clean();

        $this->assertEquals('', $setupCode);
        $this->assertEquals('Our parent instance (ServerSocket) is not active!', $output);
    }

    public function testConfigurationForParent(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);
        $form = json_decode(IPS_GetConfigurationForParent($bridgeID), true);

        $this->assertEquals([
            'Port'   => IPS_GetProperty($bridgeID, 'BridgePort'),
            'UseSSL' => false,
        ], $form);
    }

    public function testConfigurationForm(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);
        $this->assertNotEquals(null, json_decode(IPS_GetConfigurationForm($bridgeID), true));
    }

    public function testAccessories(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        //Check if the generated content matches our test file
        $this->assertEquals(json_decode(file_get_contents(__DIR__ . '/exports/None.json'), true), $bridgeInterface->DebugAccessories());
    }

    public function testSetupCode(): void
    {
        $bridgeID = $this->createBridge();

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        $setupCode = $bridgeInterface->RestartPairing();

        $this->assertTrue(preg_match('/\d{4}-\d{4}/', $setupCode) == 1);
    }

    public function testHTTPInvalidResponse(): void
    {
        $bridgeID = $this->createBridge();

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        $setupCode = $bridgeInterface->RestartPairing();

        $serverID = IPS_GetInstance($bridgeID)['ConnectionID'];

        $serverInterface = IPS\InstanceManager::getInstanceInterface($serverID);

        $clientIP = '127.0.0.1';
        $clientPort = 34455;

        $data = "Totally Invalid Packet\r\n\r\n\r\n";
        $serverInterface->PushConnect($clientIP, $clientPort);
        $serverInterface->PushPacket($data, $clientIP, $clientPort);

        $this->assertTrue($serverInterface->HasPacket());

        $this->assertEquals([
            'Type'       => 0 /* Data */,
            'Buffer'     => 'HTTP/1.1 500 Internal Server Error' . "\r\n\r\n",
            'ClientIP'   => $clientIP,
            'ClientPort' => $clientPort,
        ], $serverInterface->PeekPacket());
    }
}
