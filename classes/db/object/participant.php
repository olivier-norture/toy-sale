<?php
namespace classes\db\object;

use PDO;
use classes\db\DB;

class Participant {
    private $id;
    private $nom;
    private $pNom;
    private $adresse;
    private $cp;
    private $ville;
    private $email;
    private $tel;
    private $type;
    private $ref;

    /**
     * Construct a new Participant
     * @param int $id
     * @param Stringe $nom
     * @param String $pNom
     * @param String $adresse
     * @param String $cp
     * @param String $ville
     * @param String $email
     * @param String $tel
     * @param String $type
     */
    public function __construct($id = "", $nom = "", $pNom = "", $adresse = "", $cp = "", $ville = "", $email = "", $tel = "", $type = 0, $ref = 0){
        $this->id = $id;
        $this->nom = $nom;
        $this->pNom = $pNom;
        $this->adresse = $adresse;
        $this->cp = $cp;
        $this->ville = $ville;
        $this->email = $email;
        $this->tel = $tel;
        $this->type = $type;
        $this->ref = $ref;
    }

    public function setId($id){
        $this->id = id;
    }

    public function setNom($nom){
        $this->nom = $nom;
    }

    public function setPnom($pNom){
        $this->pNom = $pNom;
    }

    public function setAdresse($adresse){
        $this->adresse = $adresse;
    }

    public function setCP($cp){
        $this->cp = $cp;
    }

    public function setVille($ville){
        $this->ville = $ville;
    }

    public function setEmail($email){
        $this->email = $email;
    }

    public function setTel($tel){
        $this->tel = $tel;
    }

    public function setType($type){
        $this->type = $type;
    }

    public function getId(){
        return $this->id;
    }

    public function getNom(){
        return $this->nom;
    }

    public function getPnom(){
        return $this->pNom;
    }

    public function getAdresse(){
        return $this->adresse;
    }

    public function getCp(){
        return $this->cp;
    }

    public function getVille(){
        return $this->ville;
    }

    public function getEmail(){
        return $this->email;
    }

    public function getTel(){
        return $this->tel;
    }

    public function getType(){
        return $this->type;
    }
    
    public function getRef(){
        return $this->ref;
    }
    
    public function setRef($ref){
        $this->ref = $ref;
    }


    /**
     * 
     * @param type $pk
     * @return Participant
     */
    public static function searchFromPk($pk){
        $stmt = DB::getConn()->prepare("select nom, prenom from participant where pk = :pk");
        $stmt->bindValue(":pk", $pk);
        $stmt->execute();
        $row = $stmt->fetch();
        return Participant::search($row["nom"], $row["prenom"]);
    }

    /**
     * Search in the database for a Participant with a given first name and last name
     * @param string $nom The first name
     * @param string $pNom The last name
     * @return \classes\db\object\Participant An empty Participant if the method found nothing
     */
    public static function search($nom, $pNom){
        $stmt = DB::getConn()->prepare("select pk, nom, prenom, adresse, cp, ville, email, tel, type, ref from participant where nom = :nom and prenom = :prenom");
        $stmt->bindValue(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindValue(':prenom', $pNom, PDO::PARAM_STR);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT);
        if($row[0] != ""){
            return new Participant($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], $row[8], $row[9]);
        }
        else{
            return new Participant();
        }
    }

    public function checkBeforeInsert(){
        return !empty($this->nom) && !empty($this->pNom);
    }

    public function save(){
        if($this->id == null){
            $stmt = DB::getConn()->prepare("
insert into participant(nom, prenom, adresse, cp, ville, email, tel, type, ref)
values (UPPER(:nom), UPPER(:prenom), UPPER(:adresse), :cp, UPPER(:ville), UPPER(:email), REPLACE(:tel, ' ', ''), :type, :ref)");
            $stmt->bindValue(":nom", $this->nom, PDO::PARAM_STR);
            $stmt->bindValue(":prenom", $this->pNom, PDO::PARAM_STR);
            $stmt->bindValue(":adresse", $this->adresse, PDO::PARAM_STR);
            $stmt->bindValue(":cp", $this->cp, PDO::PARAM_STR);
            $stmt->bindValue(":ville", $this->ville, PDO::PARAM_STR);
            $stmt->bindValue(":email", $this->email, PDO::PARAM_STR);
            $stmt->bindValue(":tel", $this->tel, PDO::PARAM_STR);
            $stmt->bindValue(":type", $this->type, PDO::PARAM_INT);
            $stmt->bindValue(":ref", $this->ref, PDO::PARAM_INT);

            DB::getConn()->beginTransaction();
            $stmt->execute();
            //extract the id and set it to the current object
            $this->id = DB::getConn()->lastInsertId();
            DB::getConn()->commit();
        }
        else{
            $stmt = DB::getConn()->prepare("
update  participant
set     nom = UPPER(:nom),
        prenom = UPPER(:prenom),
        adresse = UPPER(:adresse),
        cp = :cp,
        ville = UPPER(:ville),
        email = UPPER(:email),
        tel = REPLACE(:tel, ' ', ''),
        type = :type,
        ref = :ref
where   pk = :pk
                    ");
            $stmt->bindValue(":nom", $this->nom, PDO::PARAM_STR);
            $stmt->bindValue(":prenom", $this->pNom, PDO::PARAM_STR);
            $stmt->bindValue(":adresse", $this->adresse, PDO::PARAM_STR);
            $stmt->bindValue(":cp", $this->cp, PDO::PARAM_STR);
            $stmt->bindValue(":ville", $this->ville, PDO::PARAM_STR);
            $stmt->bindValue(":email", $this->email, PDO::PARAM_STR);
            $stmt->bindValue(":tel", $this->tel, PDO::PARAM_STR);
            $stmt->bindValue(":type", $this->type, PDO::PARAM_STR);
            $stmt->bindValue(":pk", $this->id, PDO::PARAM_INT);
            $stmt->bindValue(":ref", $this->ref, PDO::PARAM_INT);
            
            DB::getConn()->beginTransaction();
            $stmt->execute();
            DB::getConn()->commit();
        }
    }
    
    public function isEmpty(){
        return empty($this->nom) && empty($this->pNom);
    }
    
    /**
     * Retrieves all vendor's PK
     * @return string[]
     */
    public static function getAllPK(){
        $res = array();
        $stmt = DB::getConn()->prepare("select distinct pk as pk from objet");
        $stmt->execute();
        foreach($stmt->fetchAll() as $row){
            $res[] = $row["pk"];
        }
        return $res;
    }

    public static function getAllParticipants(){
        $res = array();
        $stmt = DB::getConn()->prepare("
            select pk, nom, prenom, adresse, cp, ville, email, tel, type, ref
            from participant
        ");
        $stmt->execute();
        foreach($stmt->fetchAll() as $row){
            $res[] = new Participant($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], $row[8], $row[9]);
        }
        return $res;
    }

    /**
     * 
     * !!! ref contains the letter !!!
     * @return Participant[]
     */
    public static function getSellers(){
        $res = array();
        $stmt = DB::getConn()->prepare("
            select distinct
                participant.pk,
                participant.nom, 
                participant.prenom, 
                participant.adresse, 
                participant.cp, 
                participant.ville, 
                participant.email, 
                participant.tel, 
                participant.type, 
                participant.ref,
                bill.letter
            from participant
            join bill on (bill.customer_pk = participant.pk)
            where lower(bill.type) = 'depot'
             and bill.active = true
             and participant.ref is not null
            order by bill.letter asc, participant.ref asc
        ");
        $stmt->execute();
        foreach($stmt->fetchAll() as $row){
            $res[] = new Participant($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], $row[8], $row[10] . $row[9]);
        }
        return $res;
    }

}

?>
