<?php

declare(strict_types=1);

include_once 'base.php';

foreach (glob(__DIR__ . '/*.php') as $filename) {
    if (!in_array(basename($filename), ['autoload.php', 'base.php'])) {
        include_once $filename;
    }
}
