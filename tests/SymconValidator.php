<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class TestCaseSymconValidation extends TestCase
{
    private function isValidGUID($guid): bool
    {
        return preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/', $guid) == 1;
    }

    private function isValidName($name): bool
    {
        return preg_match('/^[A-Za-z0-9 _]*$/', $name) == 1;
    }

    private function isValidPrefix($name): bool
    {
        return preg_match('/^[A-Z0-9]*$/', $name) == 1;
    }

    private function ignoreFolders(): array
    {
        return ['..', '.', 'libs', 'docs', 'imgs', 'tests'];
    }

    protected function validateLibrary($folder): void
    {
        $library = json_decode(file_get_contents($folder . '/library.json'), true);

        $this->assertArrayHasKey('id', $library);
        $this->assertInternalType('string', $library['id']);
        $this->assertTrue($this->isValidGUID($library['id']));

        $this->assertArrayHasKey('author', $library);
        $this->assertInternalType('string', $library['author']);

        $this->assertArrayHasKey('name', $library);
        $this->assertInternalType('string', $library['name']);

        $this->assertArrayHasKey('url', $library);
        $this->assertInternalType('string', $library['url']);

        $this->assertArrayHasKey('version', $library);
        $this->assertInternalType('string', $library['version']);

        $this->assertArrayHasKey('build', $library);
        $this->assertInternalType('int', $library['build']);

        $this->assertArrayHasKey('date', $library);
        $this->assertInternalType('int', $library['date']);

        //This is purely optional
        if (!isset($library['compatibility'])) {
            $this->assertCount(7, $library);
        } else {
            $this->assertCount(8, $library);
            $this->assertInternalType('array', $library['compatibility']);
            if (isset($library['compatibility']['version'])) {
                $this->assertInternalType('string', $library['compatibility']['version']);
            }
            if (isset($library['compatibility']['date'])) {
                $this->assertInternalType('int', $library['compatibility']['date']);
            }
        }
    }

    protected function validateModule($folder): void
    {
        $module = json_decode(file_get_contents($folder . '/module.json'), true);

        $this->assertArrayHasKey('id', $module);
        $this->assertInternalType('string', $module['id']);
        $this->assertTrue($this->isValidGUID($module['id']));

        $this->assertArrayHasKey('name', $module);
        $this->assertInternalType('string', $module['name']);
        $this->assertTrue($this->isValidName($module['name']));

        $this->assertArrayHasKey('type', $module);
        $this->assertInternalType('int', $module['type']);
        $this->assertGreaterThanOrEqual(0, $module['type']);
        $this->assertLessThanOrEqual(4, $module['type']);

        $this->assertArrayHasKey('vendor', $module);
        $this->assertInternalType('string', $module['vendor']);

        $this->assertArrayHasKey('aliases', $module);
        $this->assertInternalType('array', $module['aliases']);

        $this->assertArrayHasKey('parentRequirements', $module);
        $this->assertInternalType('array', $module['parentRequirements']);
        foreach ($module['parentRequirements'] as $parentRequirement) {
            $this->assertInternalType('string', $parentRequirement);
            $this->assertTrue($this->isValidGUID($parentRequirement));
        }

        $this->assertArrayHasKey('childRequirements', $module);
        $this->assertInternalType('array', $module['childRequirements']);
        foreach ($module['childRequirements'] as $childRequirement) {
            $this->assertInternalType('string', $childRequirement);
            $this->assertTrue($this->isValidGUID($childRequirement));
        }

        $this->assertArrayHasKey('implemented', $module);
        $this->assertInternalType('array', $module['implemented']);
        foreach ($module['implemented'] as $implemented) {
            $this->assertInternalType('string', $implemented);
            $this->assertTrue($this->isValidGUID($implemented));
        }

        $this->assertArrayHasKey('prefix', $module);
        $this->assertInternalType('string', $module['prefix']);
        $this->assertTrue($this->isValidPrefix($module['prefix']));

        if (file_exists($folder . '/form.json')) {
            $this->assertTrue(json_decode(file_get_contents($folder . '/form.json')) !== null);
        }

        if (file_exists($folder . '/locale.json')) {
            $this->assertTrue(json_decode(file_get_contents($folder . '/locale.json')) !== null);
        }
    }
}
