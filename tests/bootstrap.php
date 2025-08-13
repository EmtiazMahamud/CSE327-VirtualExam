<?php

declare(strict_types=1);

// Autoload dependencies
require_once __DIR__ . '/../vendor/autoload.php';

// Set up test environment
define('TEST_MODE', true);

// Mock session functions for testing
if (!function_exists('session_start')) {
    function session_start(): bool {
        return true;
    }
}

// Mock header function for testing  
if (!function_exists('header')) {
    function header(string $header, bool $replace = true, int $response_code = 0): void {
        // Do nothing in test mode
    }
}

// Mock echo function for testing
function mockEcho(string $output): void {
    // Store output for testing
    global $testOutput;
    $testOutput = $output;
}