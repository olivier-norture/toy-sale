<?php
namespace classes\pages;

use classes\db\object\Participant;
use classes\config\Constants;
use classes\db\object\Bill;
use classes\db\DB;
use PDO;

class Bilan extends Page{

    private $restitutionRows = null;
    private $totalBuyers = null;

    protected function pageProcess() {

    }

    // Single source of truth for the page totals and the CSV export.
    public function getRestitutionRows(){
        if ($this->restitutionRows === null) {
            $stmt = DB::getConn()->prepare("
SELECT participant.REF, participant.NOM, participant.PRENOM,
       participant.VILLE, participant.EMAIL, participant.TEL,
       COALESCE(invendu.nbObjetInvendu, 0) as nbObjetInvendu,
       COALESCE(vendu.nbObjetVendu, 0) as nbObjetVendu,
       COALESCE(vendu.total, 0) as totalVente,
       bill.tax as commissionTaux,
       ROUND(COALESCE(vendu.total * (bill.tax / 100), 0), 2) as commission,
       ROUND(COALESCE(vendu.total * ( (100 - bill.tax) / 100), 0), 2) as totalRendu
    FROM bill
  JOIN participant ON (participant.PK = bill.customer_pk)
  LEFT JOIN (
  SELECT bill_id, count(1) as nbObjetVendu, sum(prix) as total
    FROM bill_objects
    JOIN objet ON (bill_objects.object_id = objet.PK)
   WHERE objet.acheteur_PK is not null
   GROUP BY bill_id
) as vendu ON (vendu.bill_id = bill.id)
  LEFT JOIN (
  SELECT bill_id, count(1) as nbObjetInvendu, sum(prix) as total
    FROM bill_objects
    JOIN objet ON (bill_objects.object_id = objet.PK)
   WHERE objet.acheteur_PK is null
   GROUP BY bill_id
) as invendu ON (invendu.bill_id = bill.id)
WHERE bill.id IN (
  SELECT max(id)
    FROM bill
   WHERE bill.`type` = 'RESITUTION'
     AND bill.active = 1
    GROUP BY customer_PK
)
;
");
            $stmt->execute();
            $this->restitutionRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return $this->restitutionRows;
    }

    public function getTotalDeposant(){
        return count($this->getRestitutionRows());
    }

    // Distinct buyers of the objects in the same restitution bills as the export rows.
    public function getTotalSellers(){
        if ($this->totalBuyers === null) {
            $stmt = DB::getConn()->prepare("
SELECT count(distinct objet.acheteur_PK) as total
    FROM bill_objects
    JOIN objet ON (bill_objects.object_id = objet.PK)
   WHERE objet.acheteur_PK is not null
     AND bill_objects.bill_id IN (
  SELECT max(id)
    FROM bill
   WHERE bill.`type` = 'RESITUTION'
     AND bill.active = 1
    GROUP BY customer_PK
);
");
            $stmt->execute();
            $this->totalBuyers = (int) $stmt->fetchColumn();
        }
        return $this->totalBuyers;
    }

    public function getTotalObjects(){
        $total = 0;
        foreach ($this->getRestitutionRows() as $row) {
            $total += (int) $row["nbObjetInvendu"] + (int) $row["nbObjetVendu"];
        }
        return $total;
    }

    public function getTotalObjectsSelled(){
        $total = 0;
        foreach ($this->getRestitutionRows() as $row) {
            $total += (int) $row["nbObjetVendu"];
        }
        return $total;
    }

    public function getTotalObjetcsSelledMoney(){
        $total = 0.0;
        foreach ($this->getRestitutionRows() as $row) {
            $total += (float) $row["totalVente"];
        }
        return number_format($total, 2) . " €";
    }

    public function exportCsv(){
        $list = array();
        $list[] = array("reference", "nom", "prenom", "ville", "email", "tel", "nbObjetInvendu",
                        "nbObjetVendu", "totalVente", "commissionTaux", "commission", "totalRendu");
        foreach ($this->getRestitutionRows() as $row) {
            $list[] = array_values($row);
        }

        $fp = fopen(Constants::$RESTITUTION_BILAN_CSV_PATH, 'w');

        foreach ($list as $fields) {
            fputcsv($fp, $fields);
        }
        flush();
        fclose($fp);

        return $list;
    }

}
