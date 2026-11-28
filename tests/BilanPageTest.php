<?php

use PHPUnit\Framework\TestCase;
use classes\pages\Bilan;
use classes\db\DB;
use classes\config\Constants;

/**
 * @runInSeparateProcess
 */
class BilanPageTest extends TestCase
{
    private $mockPdo;
    private $mockPdoStatement;

    private function fixtureRows()
    {
        return array(
            array("REF" => "A1", "NOM" => "DOE", "PRENOM" => "John", "VILLE" => "PARIS", "EMAIL" => "a@x.fr", "TEL" => "01",
                  "nbObjetInvendu" => 2, "nbObjetVendu" => 1, "totalVente" => 10.5, "commissionTaux" => 20,
                  "commission" => 2.1, "totalRendu" => 8.4),
            array("REF" => "B2", "NOM" => "ROE", "PRENOM" => "Jane", "VILLE" => "CHAMBLY", "EMAIL" => "b@x.fr", "TEL" => "02",
                  "nbObjetInvendu" => 0, "nbObjetVendu" => 0, "totalVente" => 0, "commissionTaux" => 20,
                  "commission" => 0, "totalRendu" => 0),
            array("REF" => "C3", "NOM" => "POE", "PRENOM" => "Max", "VILLE" => "LYON", "EMAIL" => "c@x.fr", "TEL" => "03",
                  "nbObjetInvendu" => 1, "nbObjetVendu" => 2, "totalVente" => 7.25, "commissionTaux" => 10,
                  "commission" => 0.73, "totalRendu" => 6.53),
        );
    }

    private function buildPage(array $rows, $prepareCalls = 2, $buyers = 2)
    {
        $this->mockPdoStatement = $this->createMock(PDOStatement::class);
        $this->mockPdoStatement->method('execute')->willReturn(true);
        $this->mockPdoStatement->method('fetchAll')->willReturn($rows);
        $this->mockPdoStatement->method('fetchColumn')->willReturn($buyers);

        $this->mockPdo = $this->createMock(PDO::class);
        $this->mockPdo->expects($this->exactly($prepareCalls))->method('prepare')->willReturn($this->mockPdoStatement);

        $reflection = new ReflectionClass(DB::class);
        $connProperty = $reflection->getProperty('conn');
        $connProperty->setAccessible(true);
        $connProperty->setValue(null, $this->mockPdo);

        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        Constants::$RESTITUTION_BILAN_CSV_PATH = sys_get_temp_dir() . '/bilan_restitution_test.csv';

        return new Bilan();
    }

    public function testTotalsFromRestitutionRows()
    {
        $page = $this->buildPage($this->fixtureRows(), 2, 2);

        $this->assertSame(3, $page->getTotalDeposant());
        $this->assertSame(2, $page->getTotalSellers());
        $this->assertSame(6, $page->getTotalObjects());
        $this->assertSame(3, $page->getTotalObjectsSelled());
        $this->assertSame("17.75 €", $page->getTotalObjetcsSelledMoney());
    }

    public function testPageTotalsEqualCsvColumnSums()
    {
        $page = $this->buildPage($this->fixtureRows(), 1);
        $list = $page->exportCsv();
        $this->assertSame(3, $page->getTotalDeposant());
        $body = array_slice($list, 1);

        $sum = function ($index) use ($body) {
            return array_sum(array_column($body, $index));
        };

        $this->assertSame(count($body), $page->getTotalDeposant());
        $this->assertSame((int) ($sum(6) + $sum(7)), $page->getTotalObjects());
        $this->assertSame((int) $sum(7), $page->getTotalObjectsSelled());
        $this->assertSame(number_format($sum(8), 2) . " €", $page->getTotalObjetcsSelledMoney());
    }

    public function testEmptyRestitution()
    {
        $page = $this->buildPage(array(), 2, 0);

        $this->assertSame(0, $page->getTotalDeposant());
        $this->assertSame(0, $page->getTotalSellers());
        $this->assertSame(0, $page->getTotalObjects());
        $this->assertSame(0, $page->getTotalObjectsSelled());
        $this->assertSame("0.00 €", $page->getTotalObjetcsSelledMoney());
    }

    // Two prepares expected: one for the restitution rows, one for the buyer count, each run once.
    public function testQueriesRunOncePerInstance()
    {
        $page = $this->buildPage($this->fixtureRows(), 2, 2);

        $page->getTotalDeposant();
        $page->getTotalSellers();
        $page->getTotalSellers();
        $page->getTotalObjects();
        $page->getTotalObjetcsSelledMoney();
        $page->exportCsv();
    }
}
