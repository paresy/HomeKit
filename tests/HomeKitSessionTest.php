<?php

declare(strict_types=1);

include_once __DIR__ . '/HomeKitBaseTest.php';

//Responses and events share the encryption counter of a session. They have to be sent in order,
//even if the kernel delivers VM_UPDATE on its message thread while a request is still processed
class HomeKitSessionTest extends HomeKitBaseTest
{
    public function testWriteWithSynchronousUpdate(): void
    {
        list($bridgeID, $lampID) = $this->createBridgeWithLamps();
        $this->setSynchronousAction($bridgeID, $lampID);

        $writer = $this->pairBridge($bridgeID);
        $writer->pairVerify(50002);
        $observer = $writer->connectAgain(50003);
        $this->subscribe([$writer, $observer], 2);

        $writer->send('PUT', '/characteristics', json_encode([
            'characteristics' => [['aid' => 2, 'iid' => 102, 'value' => true]]
        ]));

        //The response can be decrypted and is not preceded by an event about the own write
        $this->assertEquals(['HTTP/1.1 204 No Content'], array_column($writer->popMessages(), 'status'));

        //Other controllers are informed
        $this->assertEquals([['characteristics' => [['aid' => 2, 'iid' => 102, 'value' => true]]]], $observer->popEvents());

        //The session is still usable
        $this->assertEquals(['characteristics' => [['aid' => 2, 'iid' => 102, 'value' => true]]], $writer->request('GET', '/characteristics?id=2.102'));
    }

    public function testOtherChangesOfAWriteAreSentAfterTheResponse(): void
    {
        list($bridgeID, $lampID, $otherID) = $this->createBridgeWithLamps();

        //Switching the first lamp switches the second lamp as well, e.g. by a scene in Symcon
        $this->setSynchronousAction($bridgeID, $lampID, $otherID);

        $writer = $this->pairBridge($bridgeID);
        $writer->pairVerify(50002);
        $this->subscribe([$writer], 2);
        $this->subscribe([$writer], 3);

        $writer->send('PUT', '/characteristics', json_encode([
            'characteristics' => [['aid' => 2, 'iid' => 102, 'value' => true]]
        ]));

        $messages = $writer->popMessages();
        $this->assertEquals(['HTTP/1.1 204 No Content', 'EVENT/1.0 200 OK'], array_column($messages, 'status'));
        $this->assertEquals(['characteristics' => [['aid' => 3, 'iid' => 102, 'value' => true]]], $messages[1]['body']);
    }

    public function testDelayedUpdateOfTheDeviceIsNoEcho(): void
    {
        list($bridgeID, $lampID) = $this->createBridgeWithLamps();

        $writer = $this->pairBridge($bridgeID);
        $writer->pairVerify(50002);
        $observer = $writer->connectAgain(50003);
        $this->subscribe([$writer, $observer], 2);

        //The device confirms the new value after the response was sent
        $writer->request('PUT', '/characteristics', json_encode([
            'characteristics' => [['aid' => 2, 'iid' => 102, 'value' => true]]
        ]));
        $this->updateVariable($bridgeID, $lampID, true);

        $this->assertEquals([], $writer->popEvents());
        $this->assertEquals([['characteristics' => [['aid' => 2, 'iid' => 102, 'value' => true]]]], $observer->popEvents());

        //Later changes, e.g. by a wall switch, are reported again
        $this->updateVariable($bridgeID, $lampID, false);
        $this->assertEquals([['characteristics' => [['aid' => 2, 'iid' => 102, 'value' => false]]]], $writer->popEvents());
    }

    public function testDeviceReportingAnotherValueIsNotified(): void
    {
        $bridgeID = $this->createBridge();

        if (!IPS_VariableProfileExists('~Intensity.100')) {
            IPS_CreateVariableProfile('~Intensity.100', 1 /* Integer */);
            IPS_SetVariableProfileValues('~Intensity.100', 0, 100, 1);
        }
        $dimmerID = IPS_CreateVariable(1 /* Integer */);
        IPS_SetVariableCustomProfile($dimmerID, '~Intensity.100');
        IPS_SetVariableCustomAction($dimmerID, $this->createRecorderScript());

        IPS_SetProperty($bridgeID, 'Accessories', json_encode([
            ['ID' => 1, 'Type' => 'LightbulbDimmer', 'Name' => 'Dimmer', 'VariableID' => $dimmerID]
        ]));
        IPS_ApplyChanges($bridgeID);

        $writer = $this->pairBridge($bridgeID);
        $writer->pairVerify(50002);
        $writer->request('PUT', '/characteristics', json_encode([
            'characteristics' => [['aid' => 2, 'iid' => 103 /* Brightness */, 'ev' => true]]
        ]));

        //The device settles on another brightness than requested. The controller has to know
        $writer->request('PUT', '/characteristics', json_encode([
            'characteristics' => [['aid' => 2, 'iid' => 103, 'value' => 40]]
        ]));
        $this->updateVariable($bridgeID, $dimmerID, 38);
        $this->assertEquals([['characteristics' => [['aid' => 2, 'iid' => 103, 'value' => 38]]]], $writer->popEvents());

        //Reaching the requested value afterwards is a real change as well
        $this->updateVariable($bridgeID, $dimmerID, 40);
        $this->assertEquals([['characteristics' => [['aid' => 2, 'iid' => 103, 'value' => 40]]]], $writer->popEvents());
    }

    public function testNotificationForABusySessionIsQueued(): void
    {
        list($bridgeID, $lampID) = $this->createBridgeWithLamps();

        $writer = $this->pairBridge($bridgeID);
        $writer->pairVerify(50002);
        $this->subscribe([$writer], 2);

        //Another thread uses the session right now
        $semaphore = 'HomeKit.' . $bridgeID . '.127.0.0.1:50002';
        IPS_SemaphoreEnter($semaphore, 0);
        $this->updateVariable($bridgeID, $lampID, true);
        $this->assertEquals([], $writer->popMessages());
        IPS_SemaphoreLeave($semaphore);

        //The next access sends the queued notification in order
        $this->assertEquals(['characteristics' => [['aid' => 2, 'iid' => 102, 'value' => true]]], $writer->request('GET', '/characteristics?id=2.102'));
        $this->assertEquals([['characteristics' => [['aid' => 2, 'iid' => 102, 'value' => true]]]], $writer->popEvents());
    }

    public function testFailedNotificationKeepsTheSessionInSync(): void
    {
        list($bridgeID, $lampID, $otherID) = $this->createBridgeWithLamps();

        $writer = $this->pairBridge($bridgeID);
        $writer->pairVerify(50002);
        $this->subscribe([$writer], 2);
        $this->subscribe([$writer], 3);

        //Both changes are queued and applied together. Reading the deleted lamp fails after the first event was sent
        $semaphore = 'HomeKit.' . $bridgeID . '.127.0.0.1:50002';
        IPS_SemaphoreEnter($semaphore, 0);
        $this->updateVariable($bridgeID, $otherID, true);
        IPS_DeleteVariable($lampID);
        IPS\InstanceManager::getInstanceInterface($bridgeID)->MessageSink(0, $lampID, VM_UPDATE, [true, true, false]);
        IPS_SemaphoreLeave($semaphore);

        //The response and the event can be decrypted, i.e. the session was saved with the advanced counter
        $this->assertEquals(['characteristics' => [['aid' => 3, 'iid' => 102, 'value' => true]]], $writer->request('GET', '/characteristics?id=3.102'));
        $this->assertEquals([['characteristics' => [['aid' => 3, 'iid' => 102, 'value' => true]]]], $writer->popEvents());
        $this->assertEquals(['characteristics' => [['aid' => 3, 'iid' => 102, 'value' => true]]], $writer->request('GET', '/characteristics?id=3.102'));
    }

    public function testRemovingTheOwnPairingDisconnects(): void
    {
        list($bridgeID) = $this->createBridgeWithLamps();

        $writer = $this->pairBridge($bridgeID);
        $writer->pairVerify(50002);

        $writer->request('POST', '/pairings', TLVBuilder::State(TLVState::M1) . TLVBuilder::Method(TLVMethod::RemovePairing) . TLVBuilder::Identifier($writer->getControllerID()), 'application/pairing+tlv8');

        //The session locked itself after its response. The cleanup disconnects it
        IPS\InstanceManager::getInstanceInterface($bridgeID)->Cleanup();
        $this->assertEquals(['DISCONNECT'], array_column($writer->popMessages(), 'status'));
    }

    public function testFailedActionIsReported(): void
    {
        list($bridgeID, $lampID, $otherID) = $this->createBridgeWithLamps();

        //The kernel cannot run an action without a valid action ID
        IPS_SetVariableCustomAction($otherID, 0);

        $writer = $this->pairBridge($bridgeID);
        $writer->pairVerify(50002);

        $writer->send('PUT', '/characteristics', json_encode([
            'characteristics' => [
                ['aid' => 2, 'iid' => 102, 'value' => true],
                ['aid' => 3, 'iid' => 102, 'value' => true]
            ]
        ]));

        //Only the failed characteristic is reported. The other one was still written
        $messages = $writer->popMessages();
        $this->assertEquals(['HTTP/1.1 207 Multi-Status'], array_column($messages, 'status'));
        $this->assertEquals(['characteristics' => [
            ['aid' => 2, 'iid' => 102, 'status' => 0],
            ['aid' => 3, 'iid' => 102, 'status' => -70402]
        ]], $messages[0]['body']);
        $this->assertEquals([$lampID => [true]], $GLOBALS['recorded']);

        //The session is still usable
        $this->assertEquals(['characteristics' => [['aid' => 2, 'iid' => 102, 'value' => false]]], $writer->request('GET', '/characteristics?id=2.102'));
    }

    public function testActionReturningFalseIsReported(): void
    {
        list($bridgeID, $lampID) = $this->createBridgeWithLamps();

        $writer = $this->pairBridge($bridgeID);
        $writer->pairVerify(50002);
        $this->subscribe([$writer], 2);

        //The helper refuses to switch a variable which does not exist anymore
        IPS_DeleteVariable($lampID);

        $this->assertEquals(['characteristics' => [['aid' => 2, 'iid' => 102, 'status' => -70402]]], $writer->request('PUT', '/characteristics', json_encode([
            'characteristics' => [['aid' => 2, 'iid' => 102, 'value' => true]]
        ])));
    }

    public function testFailedComposedWriteIsReported(): void
    {
        $bridgeID = $this->createBridge();

        $colorID = IPS_CreateVariable(1 /* Integer */);
        IPS_SetVariableCustomProfile($colorID, '~HexColor');
        IPS_SetVariableCustomAction($colorID, 10001); //Not a valid action. Running it fails

        $lampID = IPS_CreateVariable(0 /* Boolean */);
        IPS_SetVariableCustomAction($lampID, $this->createRecorderScript());

        IPS_SetProperty($bridgeID, 'Accessories', json_encode([
            ['ID' => 1, 'Type' => 'LightbulbColor', 'Name' => 'Color', 'VariableID' => $colorID],
            ['ID' => 2, 'Type' => 'LightbulbSwitch', 'Name' => 'Lamp', 'VariableID' => $lampID]
        ]));
        IPS_ApplyChanges($bridgeID);

        $writer = $this->pairBridge($bridgeID);
        $writer->pairVerify(50002);

        //Hue and Saturation are sent as one color after all characteristics were processed
        $this->assertEquals(['characteristics' => [
            ['aid' => 2, 'iid' => 104, 'status' => -70402],
            ['aid' => 2, 'iid' => 106, 'status' => -70402],
            ['aid' => 3, 'iid' => 102, 'status' => 0]
        ]], $writer->request('PUT', '/characteristics', json_encode([
            'characteristics' => [
                ['aid' => 2, 'iid' => 104 /* Hue */, 'value' => 120],
                ['aid' => 2, 'iid' => 106 /* Saturation */, 'value' => 100],
                ['aid' => 3, 'iid' => 102, 'value' => true]
            ]
        ])));
        $this->assertEquals([$lampID => [true]], $GLOBALS['recorded']);

        //The helper refuses to send the color, if the action was removed
        IPS_SetVariableCustomAction($colorID, 0);
        $this->assertEquals(['characteristics' => [
            ['aid' => 2, 'iid' => 104, 'status' => -70402]
        ]], $writer->request('PUT', '/characteristics', json_encode([
            'characteristics' => [['aid' => 2, 'iid' => 104 /* Hue */, 'value' => 240]]
        ])));
    }

    public function testFailedReadIsReported(): void
    {
        list($bridgeID) = $this->createBridgeWithLamps();

        $writer = $this->pairBridge($bridgeID);
        $writer->pairVerify(50002);

        $this->assertEquals(['characteristics' => [
            ['aid' => 2, 'iid' => 102, 'value' => false],
            ['aid' => 9, 'iid' => 102, 'status' => -70402]
        ]], $writer->request('GET', '/characteristics?id=2.102,9.102'));
    }

    public function testFailedRequestIsAnswered(): void
    {
        list($bridgeID) = $this->createBridgeWithLamps();

        $writer = $this->pairBridge($bridgeID);
        $writer->pairVerify(50002);

        //Parsing fails on an unknown TLV type. The controller must not wait for a response until it times out
        $writer->send('POST', '/pairings', "\xFE\x00", 'application/pairing+tlv8');
        $messages = $writer->popMessages();
        $this->assertEquals(['HTTP/1.1 500 Internal Server Error'], array_column($messages, 'status'));
        $this->assertEquals(['status' => -70402], $messages[0]['body']);

        //The session is still usable
        $this->assertEquals(['characteristics' => [['aid' => 2, 'iid' => 102, 'value' => false]]], $writer->request('GET', '/characteristics?id=2.102'));
    }

    private function createBridgeWithLamps(): array
    {
        $bridgeID = $this->createBridge();

        $actionID = $this->createRecorderScript();

        $lampID = IPS_CreateVariable(0 /* Boolean */);
        IPS_SetVariableCustomAction($lampID, $actionID);

        $otherID = IPS_CreateVariable(0 /* Boolean */);
        IPS_SetVariableCustomAction($otherID, $actionID);

        IPS_SetProperty($bridgeID, 'Accessories', json_encode([
            ['ID' => 1, 'Type' => 'LightbulbSwitch', 'Name' => 'Lamp', 'VariableID' => $lampID],
            ['ID' => 2, 'Type' => 'LightbulbSwitch', 'Name' => 'Other', 'VariableID' => $otherID]
        ]));
        IPS_ApplyChanges($bridgeID);

        return [$bridgeID, $lampID, $otherID];
    }

    //An action which updates the variable (and optionally another one) right away. The kernel delivers
    //VM_UPDATE on its message thread, i.e. while the request which triggered the action is still processed
    private function setSynchronousAction(int $bridgeID, int $variableID, int $otherID = 0): void
    {
        $update = function (string $variable) use ($bridgeID)
        {
            return '$old = GetValue(' . $variable . '); SetValue(' . $variable . ', $_IPS["VALUE"]); '
                . 'IPS\\InstanceManager::getInstanceInterface(' . $bridgeID . ')->MessageSink(0, ' . $variable . ', VM_UPDATE, [$_IPS["VALUE"], $_IPS["VALUE"] !== $old, $old]);';
        };

        $content = '<?php ' . $update('$_IPS["VARIABLE"]');
        if ($otherID > 0) {
            $content .= ' ' . $update((string) $otherID);
        }

        $scriptID = IPS_CreateScript(0);
        IPS_SetScriptContent($scriptID, $content);
        IPS_SetVariableCustomAction($variableID, $scriptID);
    }

    private function subscribe(array $controllers, int $aid): void
    {
        foreach ($controllers as $controller) {
            $controller->request('PUT', '/characteristics', json_encode([
                'characteristics' => [['aid' => $aid, 'iid' => 102, 'ev' => true]]
            ]));
        }
    }
}
