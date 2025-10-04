<?php
namespace classes\pages;

use classes\db\object\Participant;
use classes\config\Constants;
use classes\db\object\Bill;
use classes\db\DB;
use PDO;

class Bilan extends Page{
    
    protected function pageProcess() {
        
    }
    
    public function getTotalDeposant(){
        $stmt = DB::getConn()->prepare("select count(distinct(vendeur_PK)) as total from objet");
        $stmt->execute();
        return $stmt->fetch()["total"];
    }
    
    public function getTotalSellers(){
        $stmt = DB::getConn()->prepare("select count(distinct(acheteur_PK)) as total from objet");
        $stmt->execute();
        return $stmt->fetch()["total"];
    }
    
    public function getTotalObjects(){
        $stmt = DB::getConn()->prepare("select count(distinct(pk)) as total from objet where vendeur_PK is not null");
        $stmt->execute();
        return $stmt->fetch()["total"];
    }
    
    public function getTotalObjectsSelled(){
        $stmt = DB::getConn()->prepare("select count(distinct(pk)) as total from objet where acheteur_PK is not null");
        $stmt->execute();
        return $stmt->fetch()["total"];
    }
    
    public function getTotalObjetcsSelledMoney(){
        $stmt = DB::getConn()->prepare("select sum(prix) as total from objet where acheteur_PK is not null;");
        $stmt->execute();
        return $stmt->fetch()["total"] . " €";
    }
    
    public function exportCsv(){
        $stmt = DB::getConn()->prepare("
SELECT participant.REF, participant.NOM, participant.PRENOM,
       participant.VILLE, participant.EMAIL, participant.TEL,
       COALESCE(invendu.nbObjetInvendu, 0) as nbObjetInvendu,
       COALESCE(vendu.nbObjetVendu, 0) as nbObjetVendu,
       COALESCE(vendu.total, 0) as totalVente,
       bill.tax as commissionTaux,
       COALESCE(vendu.total * (bill.tax / 100), 0) as commission,
       COALESCE(vendu.total * ( (100 - bill.tax) / 100), 0) as totalRendu
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
        $list = array();
        $list[] = array("reference", "nom", "prenom", "ville", "email", "tel", "nbObjetInvendu",
                        "nbObjetVendu", "totalVente", "commissionTaux", "commission", "totalRendu");
        if($stmt->rowCount() > 0){
            foreach($stmt->fetchAll(PDO::FETCH_NUM) as $row){
                $list[] = $row;
            }
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