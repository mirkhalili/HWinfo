<?php
declare(strict_types=1);
require __DIR__.'/../src/Core/Auth.php'; require __DIR__.'/../config/database.php'; require __DIR__.'/../src/Services/CsvImportService.php';
$path=$argv[1]??(__DIR__.'/../sample/hardware-info.csv');
$rows=CsvImportService::read($path);
if(count($rows)<1) throw new RuntimeException('No rows');
if(count($rows[0])!==75) throw new RuntimeException('Expected 75 columns');
echo "PASS: CSV contract validated; rows=".count($rows)." columns=".count($rows[0]).PHP_EOL;
