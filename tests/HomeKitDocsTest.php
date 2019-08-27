<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class HomeKitDocsTest extends TestCase
{
    public function testForAccessoryTests(): void
    {
        foreach (glob(__DIR__ . '/../HomeKitBridge/accessories/*.php') as $filename) {
            if (!in_array(basename($filename), ['autoload.php', 'base.php', 'bridge.php'])) {
                $className = 'HomeKit' . ucfirst(basename($filename, '.php')) . 'Test';
                $filePath = __DIR__ . '/' . $className . '.php';
                $this->assertTrue(file_exists($filePath), $className . '.php is missing!');
                include_once $filePath;

                //Load some more information
                $hapConfigClassName = 'HAPAccessoryConfiguration' . ucfirst(basename($filename, '.php'));
                $translations = call_user_func_array($hapConfigClassName . '::getTranslations', []);
                $caption = call_user_func_array($hapConfigClassName . '::getCaption', []);
                $this->assertTrue(isset($translations['de']), 'German translation for ' . $className . ' is missing!');
                $this->assertTrue(isset($translations['de'][$caption]), 'German string translation for ' . $className . ' is missing!');
                $captionDE = $translations['de'][$caption];

                //Check existence in README
                $doc = file_get_contents(__DIR__ . '/../docs/README.md');
                $linkName = strtolower(str_replace([' ', '(', ')', '/', 'ä', 'ö', 'ü'], ['-', '', '', '-', 'ae', 'oe', 'ue'], $captionDE));
                $this->assertTrue(strpos($doc, '[' . $captionDE . '][' . $linkName . ']') !== false, 'Documentation link for ' . $caption . ' is missing!');
                $this->assertTrue(strpos($doc, '[' . $linkName . ']: types/' . $linkName . '.md') !== false, 'Documentation link-ref for ' . $caption . ' is missing!');

                //Check existence in types folder
                $this->assertTrue(file_exists(__DIR__ . '/../docs/types/' . $linkName . '.md'), 'Documentation for ' . $caption . ' is missing!');
            }
        }
    }
}
