<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class HomeKitTest extends TestCase
{
    public function testForAccessoryTests(): void
    {
        foreach (glob(__DIR__ . '/../HomeKitBridge/accessories/*.php') as $filename) {
            if (!in_array(basename($filename), ['autoload.php', 'base.php'])) {
                $className = 'HomeKit' . ucfirst(basename($filename, '.php')) . 'Test';
                $filePath = __DIR__ . '/' . $className . '.php';
                $this->assertTrue(file_exists($filePath), $className . '.php is missing!');
                include_once $filePath;
                $this->assertTrue(class_exists($className), $className . ' is missing!');
            }
        }
    }
}
