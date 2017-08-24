<?php

foreach (glob(__DIR__ . '/*.php') as $filename) {
    if (basename($filename) != 'autoload.php') {
        include_once $filename;
    }
}
