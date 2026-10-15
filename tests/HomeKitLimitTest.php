<?php

declare(strict_types=1);

include_once __DIR__ . '/HomeKitBaseTest.php';

//A bridge must not expose more than 150 HAP accessory objects, the bridge itself included (HAP R17, 2.5.3.2)
class HomeKitLimitTest extends HomeKitBaseTest
{
    public function testLimitReached(): void
    {
        $bridgeID = $this->createBridge();
        $this->setAccessories($bridgeID, $this->createSwitches(1, 149));

        $this->assertEquals(range(1, 150), $this->getExportedAIDs($bridgeID));

        $form = $this->getForm($bridgeID);
        $this->assertEquals('List', $form['elements'][0]['type']);
        $this->assertEquals(['OK'], array_values(array_unique(array_column($form['elements'][0]['values'], 'Status'))));
    }

    public function testLimitExceeded(): void
    {
        $bridgeID = $this->createBridge();

        //The order inside the list must not matter. The lowest IDs are kept
        $accessories = $this->createSwitches(1, 152);
        $accessories = array_reverse($accessories);
        $this->setAccessories($bridgeID, $accessories);

        //Bridge (aid 1) plus IDs 1-149 (aid 2-150). IDs 150-152 are dropped
        $this->assertEquals(range(1, 150), $this->getExportedAIDs($bridgeID));

        $form = $this->getForm($bridgeID);
        $this->assertEquals('Label', $form['elements'][0]['type']);
        $this->assertEquals('List', $form['elements'][1]['type']);

        $statuses = [];
        foreach ($accessories as $index => $accessory) {
            $statuses[$accessory['ID']] = $form['elements'][1]['values'][$index]['Status'];
        }
        $this->assertEquals('OK', $statuses[149]);
        $this->assertEquals('Limit exceeded', $statuses[150]);
        $this->assertEquals('Limit exceeded', $statuses[152]);

        //Dropped accessories neither notify nor can be addressed
        $messages = (new ReflectionMethod('HomeKitBridge', 'GetMessageList'))->invoke(IPS\InstanceManager::getInstanceInterface($bridgeID));
        $this->assertTrue(isset($messages[$this->findVariable($accessories, 149)]));
        $this->assertFalse(isset($messages[$this->findVariable($accessories, 150)]));

        $manager = (new ReflectionProperty('HomeKitBridge', 'manager'))->getValue(IPS\InstanceManager::getInstanceInterface($bridgeID));
        $this->assertFalse($manager->readCharacteristics(150, 102)); //ID 149
        $this->expectException(Exception::class);
        $manager->readCharacteristics(151, 102); //ID 150
    }

    public function testBrokenAccessoriesDoNotCount(): void
    {
        $bridgeID = $this->createBridge();

        $accessories = $this->createSwitches(1, 150);

        //A broken accessory is not exposed, so the next one moves in
        IPS_DeleteVariable($this->findVariable($accessories, 5));
        $this->setAccessories($bridgeID, $accessories);

        $this->assertEquals(array_merge(range(1, 5), range(7, 151)), $this->getExportedAIDs($bridgeID));

        $form = $this->getForm($bridgeID);
        $this->assertEquals('List', $form['elements'][0]['type']);
    }

    private function createSwitches(int $fromID, int $toID): array
    {
        $accessories = [];
        for ($id = $fromID; $id <= $toID; $id++) {
            $variableID = IPS_CreateVariable(0 /* Boolean */);
            IPS_SetVariableCustomAction($variableID, 10001); //Any valid ID will do

            $accessories[] = [
                'ID'         => $id,
                'Type'       => 'Switch',
                'Name'       => 'Switch ' . $id,
                'VariableID' => $variableID
            ];
        }
        return $accessories;
    }

    private function findVariable(array $accessories, int $id): int
    {
        foreach ($accessories as $accessory) {
            if ($accessory['ID'] == $id) {
                return $accessory['VariableID'];
            }
        }
        throw new Exception('ID not found');
    }

    private function setAccessories(int $bridgeID, array $accessories): void
    {
        IPS_SetProperty($bridgeID, 'Accessories', json_encode($accessories));
        IPS_ApplyChanges($bridgeID);
    }

    private function getExportedAIDs(int $bridgeID): array
    {
        $aids = array_column(IPS\InstanceManager::getInstanceInterface($bridgeID)->DebugAccessories(), 'aid');
        sort($aids);
        return $aids;
    }

    private function getForm(int $bridgeID): array
    {
        return json_decode(IPS_GetConfigurationForm($bridgeID), true);
    }
}
