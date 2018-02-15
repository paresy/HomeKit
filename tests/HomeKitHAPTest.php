<?php

declare(strict_types=1);

include_once __DIR__ . '/../HomeKitBridge/hap.php';
include_once __DIR__ . '/../HomeKitBridge/manager.php';
include_once __DIR__ . '/../HomeKitBridge/characteristics/autoload.php';
include_once __DIR__ . '/../HomeKitBridge/services/autoload.php';
include_once __DIR__ . '/../HomeKitBridge/accessories/autoload.php';

use PHPUnit\Framework\TestCase;

class HomeKitHAPTest extends TestCase
{
    public function testCharacteristics(): void
    {
        foreach (glob(__DIR__ . '/../HomeKitBridge/characteristics/*.php') as $filename) {
            if (basename($filename) != 'autoload.php') {
                $className = 'HAPCharacteristic' . ucfirst(basename($filename, '.php'));
                $this->assertTrue(class_exists($className), $className . ' is missing!');
                new $className();
            }
        }
    }

    public function testServices(): void
    {
        foreach (glob(__DIR__ . '/../HomeKitBridge/services/*.php') as $filename) {
            if (basename($filename) != 'autoload.php') {
                $className = 'HAPService' . ucfirst(basename($filename, '.php'));
                $this->assertTrue(class_exists($className), $className . ' is missing!');
                new $className();
            }
        }
    }

    public function testAccessories(): void
    {
        foreach (glob(__DIR__ . '/../HomeKitBridge/accessories/*.php') as $filename) {
            if (!in_array(basename($filename), ['autoload.php', 'base.php'])) {
                $className = 'HAPAccessory' . ucfirst(basename($filename, '.php'));
                $this->assertTrue(class_exists($className), $className . ' is missing!');
                if ($className == 'HAPAccessoryBridge') {
                    new $className();
                } else {
                    new $className([]);
                }
            }
        }
    }
}
