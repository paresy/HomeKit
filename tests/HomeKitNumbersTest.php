<?php

declare(strict_types=1);

include_once __DIR__ . '/HomeKitBaseTest.php';
include_once __DIR__ . '/HomeKitTestController.php';

//Configuration number (c#) and state number (s#) as defined in HAP R17, Table 6-7
class HomeKitNumbersTest extends HomeKitBaseTest
{
    protected $dnssdModuleID = '{780B2D48-916C-4D59-AD35-5A429B2355A5}';

    public function testConfigurationNumberIgnoresValueChanges(): void
    {
        list($bridgeID, $variableID) = $this->createBridgeWithSwitch();

        $configurationNumber = $this->getAttribute($bridgeID, 'ConfigurationNumber');

        SetValue($variableID, true);
        IPS_ApplyChanges($bridgeID);

        $this->assertEquals($configurationNumber, $this->getAttribute($bridgeID, 'ConfigurationNumber'));
    }

    public function testConfigurationNumberOnStructureChange(): void
    {
        list($bridgeID, $variableID) = $this->createBridgeWithSwitch();

        $configurationNumber = $this->getAttribute($bridgeID, 'ConfigurationNumber');

        //Renaming changes the accessory information service
        $this->setAccessories($bridgeID, [
            ['ID' => 1, 'Type' => 'LightbulbSwitch', 'Name' => 'Renamed', 'VariableID' => $variableID]
        ]);

        $this->assertEquals($configurationNumber + 1, $this->getAttribute($bridgeID, 'ConfigurationNumber'));
    }

    public function testConfigurationNumberWraps(): void
    {
        list($bridgeID, $variableID) = $this->createBridgeWithSwitch();

        $this->setAttribute($bridgeID, 'ConfigurationNumber', 65535);

        $this->setAccessories($bridgeID, []);

        $this->assertEquals(1, $this->getAttribute($bridgeID, 'ConfigurationNumber'));
    }

    public function testConfigurationNumberOnRuntimeStatusChange(): void
    {
        IPS_CreateInstance($this->dnssdModuleID);
        list($bridgeID, $variableID) = $this->createBridgeWithSwitch();
        $controller = $this->pair($bridgeID);

        $configurationNumber = $this->getAttribute($bridgeID, 'ConfigurationNumber');

        //The variable is deleted without any ApplyChanges. The accessory vanishes from the attribute database
        IPS_DeleteVariable($variableID);

        $controller->pairVerify(50002);
        $accessories = $controller->request('GET', '/accessories');

        $this->assertEquals([1], array_column($accessories['accessories'], 'aid'));
        $this->assertEquals($configurationNumber + 1, $this->getAttribute($bridgeID, 'ConfigurationNumber'));
        $this->assertEquals('c#=' . ($configurationNumber + 1), $this->getPublishedRecord('c#'));
    }

    public function testMessageRegistrations(): void
    {
        list($bridgeID, $variableID) = $this->createBridgeWithSwitch();

        $messages = $this->getMessageList($bridgeID);
        $this->assertEquals([VM_UPDATE], $messages[$variableID]);
        $this->assertEquals([IPS_KERNELMESSAGE], $messages[0]);

        //Removed accessories are unregistered again
        $this->setAccessories($bridgeID, []);

        $messages = $this->getMessageList($bridgeID);
        $this->assertFalse(isset($messages[$variableID]));
        $this->assertEquals([IPS_KERNELMESSAGE], $messages[0]);
    }

    public function testStateNumberWhileDisconnected(): void
    {
        IPS_CreateInstance($this->dnssdModuleID);
        list($bridgeID, $variableID) = $this->createBridgeWithSwitch();
        $controller = $this->pair($bridgeID);

        $stateNumber = $this->getAttribute($bridgeID, 'CurrentStateNumber');

        //Multiple changes while disconnected only increment once
        $this->changeVariable($bridgeID, $variableID, true);
        $this->changeVariable($bridgeID, $variableID, false);
        $this->changeVariable($bridgeID, $variableID, true);

        $this->assertEquals($stateNumber + 1, $this->getAttribute($bridgeID, 'CurrentStateNumber'));
        $this->assertEquals('s#=' . ($stateNumber + 1), $this->getPublishedRecord('s#'));

        //After the controller connected again, the next change while disconnected increments again
        $controller->pairVerify(50002);
        $controller->disconnect();

        $this->changeVariable($bridgeID, $variableID, false);

        $this->assertEquals($stateNumber + 2, $this->getAttribute($bridgeID, 'CurrentStateNumber'));
    }

    public function testStateNumberWhileConnected(): void
    {
        list($bridgeID, $variableID) = $this->createBridgeWithSwitch();
        $controller = $this->pair($bridgeID);

        $controller->pairVerify(50002);
        $controller->request('PUT', '/characteristics', json_encode([
            'characteristics' => [['aid' => 2, 'iid' => 102, 'ev' => true]]
        ]));

        $stateNumber = $this->getAttribute($bridgeID, 'CurrentStateNumber');

        //Connected controllers receive events instead
        $this->changeVariable($bridgeID, $variableID, true);

        $this->assertEquals($stateNumber, $this->getAttribute($bridgeID, 'CurrentStateNumber'));
        $this->assertEquals([[
            'characteristics' => [['aid' => 2, 'iid' => 102, 'value' => true]]
        ]], $controller->popEvents());
    }

    public function testStateNumberOnRestart(): void
    {
        list($bridgeID, $variableID) = $this->createBridgeWithSwitch();
        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        //Unpaired accessories keep their state number
        $bridgeInterface->MessageSink(0, 0, IPS_KERNELMESSAGE, [KR_READY]);
        $this->assertEquals(1, $this->getAttribute($bridgeID, 'CurrentStateNumber'));

        $this->pair($bridgeID);
        $stateNumber = $this->getAttribute($bridgeID, 'CurrentStateNumber');

        $bridgeInterface->MessageSink(0, 0, IPS_KERNELMESSAGE, [KR_READY]);
        $this->assertEquals($stateNumber + 1, $this->getAttribute($bridgeID, 'CurrentStateNumber'));
    }

    public function testStateNumberOnRepublishWhileDisconnected(): void
    {
        IPS_CreateInstance($this->dnssdModuleID);
        list($bridgeID, $variableID) = $this->createBridgeWithSwitch();
        $controller = $this->pair($bridgeID);

        $stateNumber = $this->getAttribute($bridgeID, 'CurrentStateNumber');

        //Changing the accessories changes c#, which is re-published while no controller is connected
        $this->forgetInMemoryState($bridgeID);
        $this->setAccessories($bridgeID, []);

        $this->assertEquals($stateNumber + 1, $this->getAttribute($bridgeID, 'CurrentStateNumber'));

        //While connected, the republish does not change the state number
        $controller->pairVerify(50002);

        $this->setAccessories($bridgeID, [
            ['ID' => 1, 'Type' => 'LightbulbSwitch', 'Name' => 'Test', 'VariableID' => $variableID]
        ]);

        $this->assertEquals($stateNumber + 1, $this->getAttribute($bridgeID, 'CurrentStateNumber'));
    }

    public function testStateNumberWraps(): void
    {
        list($bridgeID, $variableID) = $this->createBridgeWithSwitch();
        $this->pair($bridgeID);

        $this->setAttribute($bridgeID, 'CurrentStateNumber', 65535);

        $this->changeVariable($bridgeID, $variableID, true);

        $this->assertEquals(1, $this->getAttribute($bridgeID, 'CurrentStateNumber'));
    }

    private function createBridgeWithSwitch(): array
    {
        $bridgeID = $this->createBridge();

        $variableID = IPS_CreateVariable(0 /* Boolean */);
        IPS_SetVariableCustomAction($variableID, 10001); //Any valid ID will do

        $this->setAccessories($bridgeID, [
            ['ID' => 1, 'Type' => 'LightbulbSwitch', 'Name' => 'Test', 'VariableID' => $variableID]
        ]);

        return [$bridgeID, $variableID];
    }

    private function pair(int $bridgeID): HomeKitTestController
    {
        $setupCode = IPS\InstanceManager::getInstanceInterface($bridgeID)->RestartPairing();

        $controller = new HomeKitTestController(IPS_GetInstance($bridgeID)['ConnectionID']);
        $controller->pairSetup($setupCode, 50001);

        return $controller;
    }

    private function setAccessories(int $bridgeID, array $accessories): void
    {
        IPS_SetProperty($bridgeID, 'Accessories', json_encode($accessories));
        IPS_ApplyChanges($bridgeID);
    }

    //IP-Symcon sends VM_UPDATE with [Value, Changed, OldValue]
    private function changeVariable(int $bridgeID, int $variableID, bool $value): void
    {
        SetValue($variableID, $value);
        IPS\InstanceManager::getInstanceInterface($bridgeID)->MessageSink(0, $variableID, VM_UPDATE, [$value, true, !$value]);
    }

    private function getAttribute(int $bridgeID, string $name)
    {
        return IPS\InstanceManager::getInstanceInterface($bridgeID)->GetPersistence()['attributes'][$name];
    }

    private function setAttribute(int $bridgeID, string $name, $value): void
    {
        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);
        $persistence = $bridgeInterface->GetPersistence();
        $persistence['attributes'][$name] = $value;
        $bridgeInterface->ApplyPersistence($persistence);
    }

    //IP-Symcon creates a new module object for every call, while the stub reuses it.
    //Clearing the in-memory DNSSD values makes sure nothing relies on state of a previous call
    private function forgetInMemoryState(int $bridgeID): void
    {
        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);
        (new ReflectionMethod('DNSSDModule', 'UpdateService'))->invoke($bridgeInterface, '', '', '', '', 0, []);
    }

    private function getMessageList(int $bridgeID): array
    {
        return (new ReflectionMethod('HomeKitBridge', 'GetMessageList'))->invoke(IPS\InstanceManager::getInstanceInterface($bridgeID));
    }

    private function getPublishedRecord(string $key): string
    {
        $dnssdID = IPS_GetInstanceListByModuleID($this->dnssdModuleID)[0];
        foreach (json_decode(IPS_GetProperty($dnssdID, 'Services'), true)[0]['TXTRecords'] as $record) {
            if (strpos($record['Value'], $key . '=') === 0) {
                return $record['Value'];
            }
        }
        return '';
    }
}
