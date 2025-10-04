<?php

use PHPUnit\Framework\TestCase;
use classes\pages\DepotJouet;
use classes\db\object\Objet;
use classes\db\object\Participant;
use classes\db\object\Bill;
use classes\db\object\PC;
use classes\pages\Basket;
use classes\db\DB;
use classes\config\Constants;
use classes\SessionManager;
use classes\utils\Date;

/**
 * @runInSeparateProcess
 */
class DepotJouetPageTest extends TestCase
{
    private $mockPdo;
    private $mockPdoStatement;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the PDOStatement
        $this->mockPdoStatement = $this->createMock(PDOStatement::class);
        $this->mockPdoStatement->method('execute')->willReturn(true);

        // Mock the PDO connection
        $this->mockPdo = $this->createMock(PDO::class);
        $this->mockPdo->method('prepare')->willReturn($this->mockPdoStatement);
        $this->mockPdo->method('beginTransaction')->willReturn(true);
        $this->mockPdo->method('commit')->willReturn(true);
        $this->mockPdo->method('lastInsertId')->willReturn('123');

        // Use Reflection to set the private static $conn property of the DB class
        $reflection = new ReflectionClass(DB::class);
        $connProperty = $reflection->getProperty('conn');
        $connProperty->setAccessible(true);
        $connProperty->setValue(null, $this->mockPdo);

        // Set dummy server and post variables
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_POST['action'] = 'add';
        $_POST['description'] = 'Test Toy';
        $_POST['prix'] = '10.5';

        // Setup session variables
        $sessionManager = new SessionManager();
        
        $acheteur = new Participant(1, 'DOE', 'John', '1 rue de test', '75001', 'PARIS', 'j.doe@email.com', '0123456789', 0, 1);
        $sessionManager->saveParticipant($acheteur);

        $pc = new PC(1, '127.0.0.1', 'A', 0);
        $sessionManager->savePc($pc);

        $bill = new Bill(new Basket(), Constants::$BILL_TYPE_DEPOT, 0, 0, 0);
        $sessionManager->saveBill($bill);
    }

    public function testAddObjectState()
    {
        // Configure the mock PDOStatement for specific queries
        $this->mockPdoStatement->method('fetch')->will($this->onConsecutiveCalls(
            ['1'], // For Objet::getNextId()
            ['id' => 1, 'ip' => '127.0.0.1', 'letter' => 'A', 'counter' => 0] // for PC::search
        ));
        $this->mockPdoStatement->method('fetchAll')->willReturn([]);

        $depotJouet = new DepotJouet();

        // Use reflection to call the private initPc method from the parent Page class
        $pageReflection = new ReflectionClass('classes\pages\Page');
        $initPcMethod = $pageReflection->getMethod('initPc');
        $initPcMethod->setAccessible(true);
        $initPcMethod->invoke($depotJouet);

        // Use reflection to call the private init method
        $reflection = new ReflectionClass(DepotJouet::class);
        $initMethod = $reflection->getMethod('init');
        $initMethod->setAccessible(true);
        $initMethod->invoke($depotJouet);

        // Call the method to be tested
        $depotJouet->addObject();

        // Use reflection to get the private basket property
        $basketProperty = $reflection->getProperty('basket');
        $basketProperty->setAccessible(true);
        /** @var Basket $basket */
        $basket = $basketProperty->getValue($depotJouet);
        
        $allObjects = $basket->getAll();
        /** @var Objet $newObject */
        $newObject = end($allObjects);

        // With the fix applied to DepotJouet::addObject, the state should be EN_VENTE
        $this->assertEquals(Objet::$EN_VENTE, $newObject->getState(), "The new object's state should be EN_VENTE");
    }
}
