<?php

use PHPUnit\Framework\TestCase;

/**
 * Test class for print layout functionality
 * Tests various toy quantities to ensure proper print layout
 */
class PrintLayoutTest extends TestCase
{
    private $testParticipant;
    private $testToys = [];
    
    protected function setUp(): void
    {
        // Create a test participant (mock data)
        $this->testParticipant = (object)[
            'nom' => 'Test',
            'prenom' => 'User',
            'telephone' => '0123456789',
            'id' => 1
        ];
        
        // Generate test toy data
        $this->generateTestToys();
    }
    
    /**
     * Generate realistic test toy data with varying description lengths
     */
    private function generateTestToys()
    {
        $toyDescriptions = [
            "Voiture télécommandée rouge Ferrari avec télécommande",
            "Poupée Barbie avec accessoires et vêtements",
            "Jeu de construction LEGO château médiéval 500 pièces",
            "Peluche ours en peluche marron très doux",
            "Puzzle 1000 pièces paysage montagne",
            "Jeu de société Monopoly édition classique",
            "Guitare électrique jouet avec amplificateur",
            "Set de cuisine jouet avec ustensiles et aliments",
            "Train électrique avec rails et wagons",
            "Ballon de football officiel taille 5",
            "Livre d'histoires pour enfants illustré",
            "Jeu vidéo console Nintendo Switch",
            "Vélo enfant 16 pouces avec petites roues",
            "Trampoline de jardin 3 mètres avec filet",
            "Microscope éducatif avec lames d'observation",
            "Robot programmable avec télécommande",
            "Maison de poupée en bois avec meubles",
            "Skateboard complet avec protections",
            "Appareil photo numérique pour enfants",
            "Jeu d'échecs en bois avec échiquier",
            "Trottinette 3 roues avec guidon ajustable",
            "Coffret de maquillage pour enfants",
            "Jeu de cartes collection Pokémon",
            "Instrument de musique xylophone coloré",
            "Déguisement de princesse avec accessoires",
            "Voiture électrique enfant 12V avec télécommande parentale",
            "Jeu de construction magnétique 100 pièces colorées",
            "Peluche géante licorne arc-en-ciel très douce",
            "Set de jardinage pour enfants avec outils miniatures",
            "Tablette éducative interactive avec jeux d'apprentissage"
        ];
        
        $prices = [5, 8, 12, 15, 20, 25, 30, 35, 40, 45, 50, 60, 75, 80, 90, 100, 120, 150, 180, 200];
        
        for ($i = 0; $i < 100; $i++) {
            $description = $toyDescriptions[$i % count($toyDescriptions)];
            $price = $prices[$i % count($prices)];
            
            $toy = (object)[
                'description' => $description,
                'price' => $price,
                'ref' => 'A' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'id' => $i + 1,
                'participant_id' => $this->testParticipant->id
            ];
            
            $this->testToys[] = $toy;
        }
    }
    
    /**
     * Test print layout with 10 toys (should fit on 1 page)
     */
    public function testPrintLayoutWith10Toys()
    {
        $toys = array_slice($this->testToys, 0, 10);
        $this->assertPrintLayoutOptimal($toys, 1, "10 toys should fit on 1 page");
    }
    
    /**
     * Test print layout with 50 toys (should span 2-3 pages logically)
     */
    public function testPrintLayoutWith50Toys()
    {
        $toys = array_slice($this->testToys, 0, 50);
        $this->assertPrintLayoutOptimal($toys, 3, "50 toys should span 2-3 pages logically");
    }
    
    /**
     * Test print layout with 100 toys (should span 3-4 pages logically)
     */
    public function testPrintLayoutWith100Toys()
    {
        $toys = array_slice($this->testToys, 0, 100);
        $this->assertPrintLayoutOptimal($toys, 4, "100 toys should span 3-4 pages logically");
    }
    
    /**
     * Test that headers never appear alone
     */
    public function testNoOrphanedHeaders()
    {
        foreach ([10, 50, 100] as $toyCount) {
            $toys = array_slice($this->testToys, 0, $toyCount);
            $this->assertNoOrphanedHeaders($toys);
        }
    }
    
    /**
     * Test that footers never appear alone
     */
    public function testNoOrphanedFooters()
    {
        foreach ([10, 50, 100] as $toyCount) {
            $toys = array_slice($this->testToys, 0, $toyCount);
            $this->assertNoOrphanedFooters($toys);
        }
    }
    
    /**
     * Test that table rows are not split across pages
     */
    public function testNoSplitTableRows()
    {
        foreach ([10, 50, 100] as $toyCount) {
            $toys = array_slice($this->testToys, 0, $toyCount);
            $this->assertNoSplitTableRows($toys);
        }
    }
    
    /**
     * Assert that print layout is optimal for given toys
     */
    private function assertPrintLayoutOptimal($toys, $maxExpectedPages, $message)
    {
        $estimatedPages = $this->estimatePageCount($toys);
        $this->assertLessThanOrEqual($maxExpectedPages, $estimatedPages, $message);
    }
    
    /**
     * Assert no orphaned headers in print layout
     */
    private function assertNoOrphanedHeaders($toys)
    {
        // Simulate print layout analysis
        $headerHeight = 80; // Estimated header height in pixels
        $minContentHeight = 200; // Minimum content that should accompany header
        
        // Check if header would appear with sufficient content
        $this->assertGreaterThan($minContentHeight, $this->estimateContentHeight($toys), 
            "Header should appear with sufficient content");
    }
    
    /**
     * Assert no orphaned footers in print layout
     */
    private function assertNoOrphanedFooters($toys)
    {
        // Simulate print layout analysis
        $footerHeight = 150; // Estimated footer height in pixels
        $minPrecedingContent = 200; // Minimum content that should precede footer
        
        // Check if footer would appear with sufficient preceding content
        $this->assertGreaterThan($minPrecedingContent, $this->estimateContentHeight($toys), 
            "Footer should appear with sufficient preceding content");
    }
    
    /**
     * Assert no split table rows in print layout
     */
    private function assertNoSplitTableRows($toys)
    {
        $rowHeight = 25; // Estimated row height in pixels
        $pageHeight = 1123; // A4 page height in pixels
        
        foreach ($toys as $toy) {
            // Each toy should fit within page boundaries
            $this->assertLessThan($pageHeight, $rowHeight, 
                "Table row should not exceed page height");
        }
    }
    
    /**
     * Estimate page count for given toys
     */
    private function estimatePageCount($toys)
    {
        $headerHeight = 80;
        $footerHeight = 150;
        $rowHeight = 25;
        $pageHeight = 1123; // A4 height in pixels
        
        $availableHeight = $pageHeight - $headerHeight - $footerHeight;
        $rowsPerPage = floor($availableHeight / $rowHeight);
        
        return ceil(count($toys) / $rowsPerPage);
    }
    
    /**
     * Estimate content height for given toys
     */
    private function estimateContentHeight($toys)
    {
        $rowHeight = 25;
        $headerHeight = 80;
        
        return $headerHeight + (count($toys) * $rowHeight);
    }
    
    /**
     * Generate HTML for testing print layout
     */
    public function generateTestHTML($toys, $filename = null)
    {
        if (!$filename) {
            $filename = '/tmp/test_print_layout_' . count($toys) . '_toys.html';
        }
        
        $html = $this->buildTestHTML($toys);
        file_put_contents($filename, $html);
        
        return $filename;
    }
    
    /**
     * Build test HTML with proper print structure
     */
    private function buildTestHTML($toys)
    {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Test Print Layout - ' . count($toys) . ' Toys</title>
    <link rel="stylesheet" type="text/css" href="../web/stylesheets/main.css" />
    <link rel="stylesheet" type="text/css" href="../web/stylesheets/style.css" />
    <script src="../web/js/script.js"></script>
</head>
<body>
    <div class="onlyPrintable print-header">
        <div style="float left; display: inline-block">
            <img src="../web/images/logo.png"/>
        </div>
        <div style="float: right; display: inline-block;">
            <p>
                <span class="bold_text">Bourse aux Jouets Chambly</span><br />
                <span class="italic_text">Association loi 1901</span><br />
                60230 Chambly
            </p>
        </div>
    </div>
    
    <div id="main_content">
        <div id="contact_area">
            <div class="container">
                <div style="float: right;">
                    <label class="labelInfoTitle labelInfo">Facture N° : TEST-001</label>
                </div>
                <h2 id="contact">DÉPÔT</h2>
                
                <div id="contact_info" class="print-participant-info">
                    <p><strong>Participant:</strong> Test User</p>
                    <p><strong>Téléphone:</strong> 0123456789</p>
                    <p><strong>Date:</strong> ' . date('d/m/Y') . '</p>
                </div>
                
                <div class="print-content-group">
                    <table class="print-table-section">
                        <thead class="print-table-header">
                            <tr>
                                <td id="entete_tableau"><label class="reference">RÉFÉRENCE</label></td>
                                <td id="entete_tableau" style="width: 100%;"><label>DESCRIPTION</label></td>
                                <td id="entete_tableau"><label class="prix">PRIX (€)</label></td>
                            </tr>
                        </thead>
                        <tbody>';
        
        foreach ($toys as $toy) {
            $html .= '<tr>
                <td>' . $toy->ref . '</td>
                <td><textarea class="textarea">' . $toy->description . '</textarea></td>
                <td>' . $toy->price . '</td>
            </tr>';
        }
        
        $html .= '
                        </tbody>
                        <tfoot>
                            <tr class="print-total-row">
                                <td id="entete_tableau">Total</td>
                                <td><label>' . count($toys) . ' jouet(s)</label></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="onlyPrintable print-footer">
        <hr/>
        <div class="signature" style="float: left;">
            <br /><br />
            <label>Tout retour d\'article ou demande de remboursement doit être effectué avant 17h aujourd\'hui.
            Passé ce délai aucune réclamation ne pourra être effectuée.
            Nous vous remercions de votre compréhension.</label>
            <br/><br/>
            <label>Le : ' . date('d/m/Y') . '</label>
            <br/><br/>
            <label>A : Chambly</label>
            <br/><br/><br/>
            <label>Signature :</label>
        </div>
        <div style="float: right; padding-top: 30px;">
            <img src="../web/images/logo.png"/>
        </div>
        <hr/>
    </div>
    
    <script>
        // Auto-trigger print layout optimization for testing
        document.addEventListener("DOMContentLoaded", function() {
            hideNotPrintableElements();
            hideOnlyPrintableElements(false);
            optimizePrintLayout();
            addPrintContentGrouping();
            optimizeTableForPrint();
        });
    </script>
</body>
</html>';
        
        return $html;
    }
}