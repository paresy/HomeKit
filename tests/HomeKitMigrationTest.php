<?php

declare(strict_types=1);

include_once __DIR__ . '/HomeKitBaseTest.php';

class HomeKitMigrationTest extends HomeKitBaseTest
{
    public function testFreshInstanceNeedsNoMigration(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        $this->assertFalse(IPS\InstanceManager::migrateInstance($bridgeID));
    }

    public function testLegacyPropertyMigration(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        $colorID = IPS_CreateVariable(1 /* Integer */);
        IPS_SetVariableCustomProfile($colorID, '~HexColor');
        IPS_SetVariableCustomAction($colorID, 10001); //Any valid ID will do

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        //Stage the persistence of a pre-2.0 module version
        $bridgeInterface->ApplyPersistence([
            'configuration' => [
                'AccessoryLightbulbColor' => json_encode([
                    [
                        'ID'         => 2,
                        'Name'       => 'Test',
                        'VariableID' => $colorID
                    ]
                ]),
                'ConfigurationNumber' => '7',
                'ConfigurationHash'   => 'legacy'
            ]
        ]);

        $this->assertTrue(IPS\InstanceManager::migrateInstance($bridgeID));

        //The legacy row was merged into the unified property with aid = ID + 1
        $accessories = json_decode(IPS_GetProperty($bridgeID, 'Accessories'), true);
        $this->assertEquals(1, count($accessories));
        $this->assertEquals(1, $accessories[0]['ID']);
        $this->assertEquals('LightbulbColor', $accessories[0]['Type']);
        $this->assertEquals('Test', $accessories[0]['Name']);
        $this->assertEquals($colorID, $accessories[0]['VariableID']);

        //doMigrate of the type ran (BrightnessID was added later)
        $this->assertEquals(0, $accessories[0]['BrightnessID']);

        //Legacy keys are gone, the configuration number moved into its attribute
        $persistence = $bridgeInterface->GetPersistence();
        $this->assertArrayNotHasKey('AccessoryLightbulbColor', $persistence['configuration']);
        $this->assertArrayNotHasKey('ConfigurationNumber', $persistence['configuration']);
        $this->assertArrayNotHasKey('ConfigurationHash', $persistence['configuration']);
        $this->assertEquals(7, $persistence['attributes']['ConfigurationNumber']);

        //A second migration is a no-op
        $this->assertFalse(IPS\InstanceManager::migrateInstance($bridgeID));

        IPS_ApplyChanges($bridgeID);

        //The configuration number was bumped exactly once for the changed accessory set
        $persistence = $bridgeInterface->GetPersistence();
        $this->assertEquals(8, $persistence['attributes']['ConfigurationNumber']);

        //The exported accessories keep their aids (old ID 2 stays aid 2)
        $base = json_decode(file_get_contents(__DIR__ . '/exports/None.json'), true);
        $accessory = json_decode(file_get_contents(__DIR__ . '/exports/LightbulbColor.json'), true);
        $this->assertEquals(array_merge($base, $accessory), $bridgeInterface->DebugAccessories());
    }

    public function testApplyChangesDoesNotModifyProperties(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        $boolID = IPS_CreateVariable(0 /* Boolean */);
        IPS_SetVariableCustomAction($boolID, 10001); //Any valid ID will do

        IPS_SetProperty($bridgeID, 'Accessories', json_encode([
            [
                'ID'         => 1,
                'Type'       => 'LightbulbSwitch',
                'Name'       => 'Test',
                'VariableID' => $boolID
            ]
        ]));
        IPS_ApplyChanges($bridgeID);

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        $configuration = $bridgeInterface->GetConfiguration();
        $persistence = $bridgeInterface->GetPersistence();

        //Repeated applies neither modify properties nor bump the configuration number
        IPS_ApplyChanges($bridgeID);
        IPS_ApplyChanges($bridgeID);

        $this->assertEquals($configuration, $bridgeInterface->GetConfiguration());
        $this->assertEquals($persistence, $bridgeInterface->GetPersistence());

        //The first accessory of a fresh install gets ID 1, which becomes aid 2 next to the bridge
        $aids = array_column($bridgeInterface->DebugAccessories(), 'aid');
        $this->assertEquals([1, 2], $aids);
    }
}
