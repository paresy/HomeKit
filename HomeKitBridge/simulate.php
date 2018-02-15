<?php

declare(strict_types=1);

if (defined('PHPUNIT_TESTSUITE')) {
    trait Simulate
    {
        public function DebugAccessories(): array
        {
            return $this->manager->getAccessories();
        }
    }
} else {
    trait Simulate
    {
    }
}
