<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class HomeKitTest extends TestCase
{
    public function testForAccessoryTests(): void
    {
        foreach (glob(__DIR__ . '/../HomeKitBridge/accessories/*.php') as $filename) {
            if (!in_array(basename($filename), ['autoload.php', 'base.php', 'switch.php'])) {
                $className = 'HomeKit' . ucfirst(basename($filename, '.php')) . "Test";
                $this->assertTrue(file_exists($className . ".php"), $className . '.php is missing!');
                include_once __DIR__ . '/' . $className . '.php';
                $this->assertTrue(class_exists($className), $className . ' is missing!');
            }
        }
    }
}
