<?php

declare(strict_types=1);

include_once __DIR__ . '/stubs/ConstantStubs.php';
include_once __DIR__ . '/stubs/GlobalStubs.php';
include_once __DIR__ . '/stubs/MessageStubs.php';
include_once __DIR__ . '/stubs/KernelStubs.php';
include_once __DIR__ . '/stubs/ModuleStubs.php';
include_once __DIR__ . '/HomeKitTestController.php';

use PHPUnit\Framework\TestCase;

class HomeKitBaseTest extends TestCase
{
    protected $bridgeModuleID = '{7FC71134-CFD0-4909-819C-B794FE067FBC}';
    protected $serverModuleID = '{8062CF2B-600E-41D6-AD4B-1BA66C32D6ED}';

    public function setUp(): void
    {
        //Reset
        IPS\Kernel::reset();

        //Register our i/o stubs for testing
        IPS\ModuleLoader::loadLibrary(__DIR__ . '/stubs/IOStubs/library.json');

        //Register our core stubs for testing
        IPS\ModuleLoader::loadLibrary(__DIR__ . '/stubs/CoreStubs/library.json');

        //Register our library we need for testing
        IPS\ModuleLoader::loadLibrary(__DIR__ . '/../library.json');

        parent::setUp();
    }

    //The console creates the ServerSocket based on GetCompatibleParents. Tests have to do this themselves
    protected function createBridge(): int
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);
        $serverID = IPS_CreateInstance($this->serverModuleID);
        IPS_ConnectInstance($bridgeID, $serverID);

        return $bridgeID;
    }

    //Starts pairing and pairs a simulated controller. Use pairVerify on the result to connect
    protected function pairBridge(int $bridgeID): HomeKitTestController
    {
        $setupCode = IPS\InstanceManager::getInstanceInterface($bridgeID)->RestartPairing();

        $controller = new HomeKitTestController(IPS_GetInstance($bridgeID)['ConnectionID']);
        $controller->pairSetup($setupCode, 50001);

        return $controller;
    }

    protected function getBridgeAttribute(int $bridgeID, string $name)
    {
        return IPS\InstanceManager::getInstanceInterface($bridgeID)->GetPersistence()['attributes'][$name];
    }

    //Symcon sends VM_UPDATE with [Value, Changed, OldValue] on every update, also when the value did not change
    protected function updateVariable(int $bridgeID, int $variableID, $value): void
    {
        $oldValue = GetValue($variableID);
        SetValue($variableID, $value);
        IPS\InstanceManager::getInstanceInterface($bridgeID)->MessageSink(0, $variableID, VM_UPDATE, [$value, $value !== $oldValue, $oldValue]);
    }

    //An action script, which records every requested value in $GLOBALS['recorded'][VariableID] without changing the variable
    protected function createRecorderScript(): int
    {
        $scriptID = IPS_CreateScript(0);
        IPS_SetScriptContent($scriptID, '<?php $GLOBALS[\'recorded\'][$_IPS[\'VARIABLE\']][] = $_IPS[\'VALUE\'];');
        $GLOBALS['recorded'] = [];
        return $scriptID;
    }

    protected function getBridgeManager(int $bridgeID): HomeKitManager
    {
        return (new ReflectionProperty('HomeKitBridge', 'manager'))->getValue(IPS\InstanceManager::getInstanceInterface($bridgeID));
    }

    public function testNop(): void
    {
        $this->assertTrue(true);
    }
}
