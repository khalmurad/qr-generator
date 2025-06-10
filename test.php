<?php
declare(strict_types=1);

// Test script for QR code generator

// Import required classes for QR code generation (will be used after autoload)
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

// Check PHP version
echo "Checking PHP version...\n";
if (PHP_VERSION_ID < 80300) {
    echo "ERROR: This application requires PHP 8.3 or higher. Current version: " . PHP_VERSION . "\n";
    exit(1);
} else {
    echo "SUCCESS: PHP version " . PHP_VERSION . " is compatible.\n";
}

// Check if required directories exist
echo "\nChecking required directories...\n";
$qrDir = __DIR__ . '/qrcodes';
if (!is_dir($qrDir)) {
    echo "Creating qrcodes directory...\n";
    if (mkdir($qrDir, 0755, true)) {
        echo "SUCCESS: qrcodes directory created.\n";
    } else {
        echo "ERROR: Failed to create qrcodes directory.\n";
        exit(1);
    }
} else {
    echo "SUCCESS: qrcodes directory exists.\n";
}

// Check if Composer dependencies are installed
echo "\nChecking Composer dependencies...\n";
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "ERROR: Composer dependencies not installed. Please run 'composer install'.\n";
    exit(1);
} else {
    echo "SUCCESS: Composer dependencies are installed.\n";
}

// Test QR code generation
echo "\nTesting QR code generation...\n";
$testUrl = 'https://example.com';
$testFilename = $qrDir . '/test.png';

try {
    // Load dependencies
    require_once __DIR__ . '/vendor/autoload.php';

    echo "Creating QR code using endroid/qr-code library...\n";

    $qrCode = QrCode::create($testUrl)
        ->setSize(300)
        ->setMargin(10);

    $writer = new PngWriter();
    $result = $writer->write($qrCode);

    if (file_put_contents($testFilename, $result->getString()) === false) {
        echo "ERROR: Failed to save QR code image.\n";
        exit(1);
    }

    echo "SUCCESS: QR code generated and saved to $testFilename.\n";

    // Clean up
    unlink($testFilename);
    echo "SUCCESS: Test QR code file removed.\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

// All tests passed
echo "\nAll tests passed! The QR code generator is ready to use.\n";
echo "Open index.php in your web browser to start generating QR codes.\n";
