<?php

declare(strict_types=1);

include_once __DIR__ . '/HomeKitBaseTest.php';

class HomeKitStatelessProgrammableSwitchTest extends HomeKitBaseTest
{
    public function testAccessory(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        $vid = IPS_CreateVariable(1 /* Integer */);

        IPS_SetProperty($bridgeID, 'Accessories', json_encode([
            [
                'ID'         => 2,
                'Type'       => 'StatelessProgrammableSwitch',
                'Name'       => 'Test',
                'VariableID' => $vid
            ]
        ]));
        IPS_ApplyChanges($bridgeID);

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        $base = json_decode(file_get_contents(__DIR__ . '/exports/None.json'), true);
        $accessory = json_decode(file_get_contents(__DIR__ . '/exports/StatelessProgrammableSwitch.json'), true);

        //Check if the generated content matches our test file
        $this->assertEquals(array_merge($base, $accessory), $bridgeInterface->DebugAccessories());
    }

    public function testAccessoryInvalidValue(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        $vid = IPS_CreateVariable(1 /* Integer */);

        SetValue($vid, -5);

        IPS_SetProperty($bridgeID, 'Accessories', json_encode([
            [
                'ID'         => 2,
                'Type'       => 'StatelessProgrammableSwitch',
                'Name'       => 'Test',
                'VariableID' => $vid
            ]
        ]));
        IPS_ApplyChanges($bridgeID);

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        $base = json_decode(file_get_contents(__DIR__ . '/exports/None.json'), true);
        $accessory = json_decode(file_get_contents(__DIR__ . '/exports/StatelessProgrammableSwitch.json'), true);

        //Check if the generated content matches our test file
        $this->assertEquals(array_merge($base, $accessory), $bridgeInterface->DebugAccessories());
    }

    //Every press is an event, also when it is the same kind of press as before (issue #81)
    public function testEveryPressIsAnEvent(): void
    {
        list($bridgeID, $buttonID, $switchID) = $this->createBridgeWithButtonAndSwitch();

        $controller = $this->pairBridge($bridgeID);
        $controller->pairVerify(50002);
        $controller->request('PUT', '/characteristics', json_encode([
            'characteristics' => [
                ['aid' => 2, 'iid' => 102, 'ev' => true], //ProgrammableSwitchEvent
                ['aid' => 3, 'iid' => 102, 'ev' => true], //On
                ['aid' => 4, 'iid' => 102, 'ev' => true], //AirQuality, sharing the variable of the button
            ]
        ]));

        //Two single presses in a row do not change the value. The air quality sharing the variable is a state
        //and must not be notified for an unchanged value
        $this->updateVariable($bridgeID, $buttonID, 0);
        $this->updateVariable($bridgeID, $buttonID, 0);

        $press = ['characteristics' => [['aid' => 2, 'iid' => 102, 'value' => 0]]];
        $this->assertEquals([$press, $press], $controller->popEvents());

        //States are only notified when they change
        $this->updateVariable($bridgeID, $switchID, false);
        $this->assertEquals([], $controller->popEvents());

        $this->updateVariable($bridgeID, $switchID, true);
        $this->assertEquals([['characteristics' => [['aid' => 3, 'iid' => 102, 'value' => true]]]], $controller->popEvents());
    }

    public function testPressWhileDisconnectedIncrementsStateNumber(): void
    {
        list($bridgeID, $buttonID, $switchID) = $this->createBridgeWithButtonAndSwitch();
        $this->pairBridge($bridgeID);

        $stateNumber = $this->getBridgeAttribute($bridgeID, 'CurrentStateNumber');

        //An unchanged state is no state change
        $this->updateVariable($bridgeID, $switchID, false);
        $this->assertEquals($stateNumber, $this->getBridgeAttribute($bridgeID, 'CurrentStateNumber'));

        //A press is, even with the same value
        $this->updateVariable($bridgeID, $buttonID, 0);
        $this->assertEquals($stateNumber + 1, $this->getBridgeAttribute($bridgeID, 'CurrentStateNumber'));
    }

    private function createBridgeWithButtonAndSwitch(): array
    {
        $bridgeID = $this->createBridge();

        $buttonID = IPS_CreateVariable(1 /* Integer */);

        $switchID = IPS_CreateVariable(0 /* Boolean */);
        IPS_SetVariableCustomAction($switchID, 10001); //Any valid ID will do

        IPS_SetProperty($bridgeID, 'Accessories', json_encode([
            ['ID' => 1, 'Type' => 'StatelessProgrammableSwitch', 'Name' => 'Button', 'VariableID' => $buttonID],
            ['ID' => 2, 'Type' => 'Switch', 'Name' => 'Switch', 'VariableID' => $switchID],
            ['ID' => 3, 'Type' => 'AirQualitySensor', 'Name' => 'Air', 'VariableID' => $buttonID]
        ]));
        IPS_ApplyChanges($bridgeID);

        return [$bridgeID, $buttonID, $switchID];
    }
}
