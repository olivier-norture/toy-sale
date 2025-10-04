<?php

use PHPUnit\Framework\TestCase;
use classes\db\DB;
use classes\db\object\PC;

class IndexPageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Set a dummy REMOTE_ADDR for testing
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        // Mock the PDOStatement
        $mockPdoStatement = $this->createMock(PDOStatement::class);
        $mockPdoStatement->method('execute')->willReturn(true);
        $mockPdoStatement->method('rowCount')->willReturn(1); // Simulate one row
        $mockPdoStatement->method('fetchAll')->willReturn([
            ['id' => 1, 'key' => 'SOME_KEY', 'value' => 'SOME_VALUE'] // Simulate a row for server_vars
        ]);
        $mockPdoStatement->method('fetch')->willReturn([
            'id' => 1,
            'ip' => '127.0.0.1',
            'letter' => 'A',
            'counter' => 0 // Add the missing 'counter' key
        ]); // Simulate a row for PC::search

        // Mock the PDO connection
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->method('prepare')->willReturn($mockPdoStatement);
        $mockPdo->method('beginTransaction')->willReturn(true);
        $mockPdo->method('commit')->willReturn(true);
        $mockPdo->method('lastInsertId')->willReturn('1');

        // Use Reflection to set the private static $conn property of the DB class
        $reflection = new ReflectionClass(DB::class);
        $connProperty = $reflection->getProperty('conn');
        $connProperty->setAccessible(true);
        $connProperty->setValue(null, $mockPdo);

        // Mock the PC class and its static search method
        // Since static methods cannot be directly mocked with PHPUnit, we'll mock the PDOStatement->fetch() result
        // to return a dummy PC row when PC::search() queries the database.
        // The PC::search() method itself will still be called, but its database interaction will be mocked.
    }

    public function testIndexPageLoadsSuccessfully()
    {
        // Start output buffering to capture the HTML output
        ob_start();

        // Include the index.php file
        // This simulates a request to the page
        require '/root/bourseauxjoeuts/web/index.php';

        // Get the captured output and clean the buffer
        $output = ob_get_clean();

        // Assert that the output is not empty
        $this->assertNotEmpty($output, 'The index.php page should produce some output.');

        // Assert that the output contains some expected HTML or text
        // For example, check for the title or a specific element
        $this->assertStringContainsString('<title>Bourse aux Jouets Chambly</title>', $output, 'The index.php page should contain the correct title.');
    }
}