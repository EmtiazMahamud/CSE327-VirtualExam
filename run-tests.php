<?php

declare(strict_types=1);

/**
 * Test Runner Script
 * 
 * This script provides a simple way to run PHPUnit tests
 * without requiring Composer to be installed globally.
 */

echo "=== PHP Unit Test Runner ===\n";
echo "Running tests for view_exams.php\n\n";

// Check if vendor directory exists
if (!is_dir(__DIR__ . '/vendor')) {
    echo "❌ Vendor directory not found. Please run 'composer install' first.\n";
    echo "If you don't have Composer installed, download it from: https://getcomposer.org/\n";
    exit(1);
}

// Check if PHPUnit is available
$phpunitPath = __DIR__ . '/vendor/bin/phpunit';
if (!file_exists($phpunitPath)) {
    echo "❌ PHPUnit not found. Please run 'composer install' first.\n";
    exit(1);
}

// Run PHPUnit tests
echo "🧪 Running PHPUnit tests...\n";
echo str_repeat("-", 50) . "\n";

$command = escapeshellarg($phpunitPath) . ' --configuration phpunit.xml --testdox';
$output = [];
$returnCode = 0;

exec($command . ' 2>&1', $output, $returnCode);

// Display output
foreach ($output as $line) {
    echo $line . "\n";
}

echo str_repeat("-", 50) . "\n";

if ($returnCode === 0) {
    echo "✅ All tests passed!\n";
} else {
    echo "❌ Some tests failed. Return code: $returnCode\n";
}

echo "\nTest run completed.\n";