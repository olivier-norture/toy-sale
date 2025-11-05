<?php
/**
 * Generate test HTML files for different toy quantities
 * This helps test print layout scenarios manually
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/PrintLayoutTest.php';

// Create test instance
$test = new PrintLayoutTest();

// Generate test files for different toy quantities
$quantities = [10, 50, 100];

foreach ($quantities as $quantity) {
    echo "Generating test file for $quantity toys...\n";
    
    $filename = __DIR__ . "/test_print_layout_{$quantity}_toys.html";
    $test->generateTestHTML(array_slice($test->testToys, 0, $quantity), $filename);
    
    echo "Generated: $filename\n";
}

echo "\nTest files generated successfully!\n";
echo "Open these files in a browser and use Print Preview to test the layout:\n";

foreach ($quantities as $quantity) {
    echo "- test_print_layout_{$quantity}_toys.html\n";
}

echo "\nTesting criteria:\n";
echo "✓ Headers should never appear alone (always with toy content)\n";
echo "✓ Footers should never appear alone (always with summary + some toys)\n";
echo "✓ Table rows should not be split across pages\n";
echo "✓ No blank pages in print output\n";
echo "✓ Optimal space utilization on A4 format\n";
?>