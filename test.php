<?php
declare(strict_types=1);

/**
 * QR Code Generator - Test Script
 *
 * This script tests the functionality of the QR code generator application.
 * It performs the following checks:
 * 1. Verifies PHP version compatibility (requires PHP 8.3+)
 * 2. Checks if required directories exist and creates them if needed:
 *    - qrcodes directory (for QR code generation)
 *    - uploads directory (for file upload functionality)
 * 3. Verifies that Composer dependencies are installed
 * 4. Tests QR code generation using the Endroid QR Code library
 *
 * Run this script from the command line to verify that your environment
 * is properly set up for the QR code generator application.
 */

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

// Check qrcodes directory
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

// Check uploads directory
$uploadsDir = __DIR__ . '/uploads';
if (!is_dir($uploadsDir)) {
    echo "Creating uploads directory...\n";
    if (mkdir($uploadsDir, 0755, true)) {
        echo "SUCCESS: uploads directory created.\n";
    } else {
        echo "ERROR: Failed to create uploads directory.\n";
        exit(1);
    }
} else {
    echo "SUCCESS: uploads directory exists.\n";
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

    // Create a QR code object with the test URL
    // - setSize(300): Sets the size of the QR code to 300 pixels
    // - setMargin(10): Sets a 10-pixel margin around the QR code
    $qrCode = QrCode::create($testUrl)
        ->setSize(300)
        ->setMargin(10);

    // Create a PNG writer to convert the QR code to a PNG image
    $writer = new PngWriter();

    // Generate the QR code image
    $result = $writer->write($qrCode);

    // Save the QR code image to a file
    if (file_put_contents($testFilename, $result->getString()) === false) {
        echo "ERROR: Failed to save QR code image.\n";
        exit(1);
    }

    echo "SUCCESS: QR code generated and saved to $testFilename.\n";

    // Clean up the test file to avoid leaving temporary files
    unlink($testFilename);
    echo "SUCCESS: Test QR code file removed.\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

// All tests passed
echo "\nAll tests passed! The QR code generator is ready to use.\n";
echo "Open index.php in your web browser to start generating QR codes.\n";

/**
 * Note: This test script verifies the basic functionality required by the QR code generator.
 * The main application (index.php and generate.php) provides a web interface for users
 * to create QR codes with custom titles and download them as PDF files, as well as
 * upload files (PNG, JPG, JPEG, PDF, PPT, PPTX, DOC, DOCX) and get shareable links.
 *
 * If all tests pass, your environment is correctly set up to run the application.
 */
