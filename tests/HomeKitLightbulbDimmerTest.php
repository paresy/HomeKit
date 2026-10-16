<?php

declare(strict_types=1);

include_once __DIR__ . '/HomeKitBaseTest.php';

class HomeKitLightbulbDimmerTest extends HomeKitBaseTest
{
    public function testAccessory(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        $vid = IPS_CreateVariable(1 /* Integer */);

        //Currently stubs do not provide default profiles
        if (!IPS_VariableProfileExists('~Intensity.100')) {
            IPS_CreateVariableProfile('~Intensity.100', 1 /* Integer */);
            IPS_SetVariableProfileValues('~Intensity.100', 0, 100, 1);
        }

        IPS_SetVariableCustomProfile($vid, '~Intensity.100'); //Any valid profile will do
        IPS_SetVariableCustomAction($vid, 10001); //Any valid ID will do

        IPS_SetProperty($bridgeID, 'Accessories', json_encode([
            [
                'ID'         => 1,
                'Type'       => 'LightbulbDimmer',
                'Name'       => 'Test',
                'VariableID' => $vid
            ]
        ]));
        IPS_ApplyChanges($bridgeID);

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        $base = json_decode(file_get_contents(__DIR__ . '/exports/None.json'), true);
        $accessory = json_decode(file_get_contents(__DIR__ . '/exports/LightbulbDimmer.json'), true);

        //Check if the generated content matches our test file
        $this->assertEquals(array_merge($base, $accessory), $bridgeInterface->DebugAccessories());
    }

    public function testAccessoryBroken(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        $vid = IPS_CreateVariable(1 /* Integer */);

        IPS_SetProperty($bridgeID, 'Accessories', json_encode([
            [
                'ID'         => 1,
                'Type'       => 'LightbulbDimmer',
                'Name'       => 'Test',
                'VariableID' => $vid /* The profile/action is missing */
            ]
        ]));
        IPS_ApplyChanges($bridgeID);

        $bridgeInterface = IPS\InstanceManager::getInstanceInterface($bridgeID);

        //Check if the generated content matches our test file
        $this->assertEquals(json_decode(file_get_contents(__DIR__ . '/exports/None.json'), true), $bridgeInterface->DebugAccessories());
    }

    //Presentations may omit parameters, e.g. a value presentation without MIN/MAX. This must not cause warnings
    public function testPresentationWithoutBounds(): void
    {
        $bridgeID = IPS_CreateInstance($this->bridgeModuleID);

        $vid = IPS_CreateVariable(1 /* Integer */);
        IPS_SetVariableCustomPresentation($vid, ['PRESENTATION' => VARIABLE_PRESENTATION_VALUE_PRESENTATION]);
        IPS_SetVariableCustomAction($vid, 10001); //Any valid ID will do

        IPS_SetProperty($bridgeID, 'Accessories', json_encode([
            ['ID' => 1, 'Type' => 'LightbulbDimmer', 'Name' => 'Test', 'VariableID' => $vid]
        ]));

        set_error_handler(function ($errno, $errstr)
        {
            throw new ErrorException($errstr, 0, $errno);
        }, E_WARNING);
        try {
            IPS_ApplyChanges($bridgeID);
            $form = json_decode(IPS_GetConfigurationForm($bridgeID), true);
        } finally {
            restore_error_handler();
        }

        $values = [];
        foreach ($form['elements'] as $element) {
            if (($element['name'] ?? '') == 'Accessories') {
                $values = $element['values'];
            }
        }
        $this->assertEquals('Presentation not dimmable', $values[0]['Status']);

        //Not dimmable accessories are not exported
        $this->assertEquals(json_decode(file_get_contents(__DIR__ . '/exports/None.json'), true), IPS\InstanceManager::getInstanceInterface($bridgeID)->DebugAccessories());
    }
}
