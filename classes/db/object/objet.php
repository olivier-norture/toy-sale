<?php
namespace classes\db\object;

use PDO;
use classes\db\DB;
use classes\utils\Logger;
use classes\config\Constants;
use classes\db\object\objetUtils;
use classes\utils\Date;

class Objet{
    private $pk;
    private $description;
    private $prix;
    private $date_baj;
    private $date_depot;
    private $date_vente;
    private $date_restitution;
    private $vendeur_PK;
    private $acheteur_PK;
    private $redacteurDepot_PK;
    private $redacteurVente_PK;
    private $redacteurRestitution_PK;
    private $id;
    private $letter;
    private $ref;
    
    public function __construct(
        $description, $prix, $vendeur_pk, $acheteur_pk="", $date_depot ="",
        $date_vente="", $date_restitution="", $pk_redacteur_depot="",
        $pk_redacteur_vente="", $pk_redacteur_restitution="", $pk = "", $id = "",
        $letter = null, $ref = "")
        {
        $this->pk = $pk;
        $this->description = $description;
        $this->prix = $prix;
        $this->date_depot = $date_depot;
        $this->date_vente = $date_vente;
        $this->date_restitution = $date_restitution;
        $this->vendeur_PK = $vendeur_pk;
        $this->acheteur_PK = $acheteur_pk;
        $this->redacteurDepot_PK = $pk_redacteur_depot;
        $this->redacteurVente_PK = $pk_redacteur_vente;
        $this->redacteurRestitution_PK = $pk_redacteur_restitution;
        $this->date_baj = "";
        $this->id = $id;
        $this->letter = $letter == null ? Constants::$DEFAULT_LETTER : $letter;
        $this->ref = $ref;
    }

    public function setVendeur($pk){
        $this->vendeur_PK = $pk;
    }

    public function setAcheteur($pk){
        $this->acheteur_PK = $pk;
    }

    public function setRedacteurDepot($pk){
        $this->redacteurDepot_PK = $pk;
    }

    public function setRedacteurVente($pk){
        $this->redacteurVente_PK = $pk;
    }

    public function setRedacteurRestitution($pk){
        $this->redacteurRestitution_PK = $pk;
    }
    
    /**
     * Set the ref for the current object
     * @param Participant $vendeur the object's seller
     * @param string $overwriteLetter 
     */
    public function setRef($vendeur, $overwriteLetter){
        $ref = objetUtils::getObjectRef($this, $vendeur, $overwriteLetter);
        $this->ref = $ref;
    }

    public static function objectFactory($id){
        return new Objet("", "", "", "", "", "", "", "", "", "", $id);
    }

    /**
     * Search for an object with the given PK
     * @param string $pk The obect's PK to find
     * @return \classes\db\object\Objet The object with the given PK
     */
    public static function searchPk($id){
        $stmt = DB::getConn()->prepare("
select pk, designation, prix, vendeur_pk, acheteur_pk, date_depot, date_vente,
       date_restitution, redacteurDepot_PK, redacteurVente_PK, redacteurRestitution_PK,
       id, letter, ref
  from objet
 where pk = :pk");
        $stmt->bindValue("pk", $id);
        $stmt->execute();
        
        $row = $stmt->fetch();
        
        Logger::log("objet", "DEBUG", "Retrieves object id '$row[0]' from '$id'");
        
        return new Objet($row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], $row[8], $row[9], $row[10], $row[0], $row[11], $row[12], $row["ref"]);
    }
    
    /**
     * Search for an object with a ref like "A001-001"
     * @param string $ref The ref to find
     * @return \classes\db\object\Objet The object with the given ref
     */
    public static function searchRef($ref){
        $stmt = DB::getConn()->prepare("
select pk, designation, prix, vendeur_pk, acheteur_pk, date_depot, date_vente,
       date_restitution, redacteurDepot_PK, redacteurVente_PK, redacteurRestitution_PK,
       id, letter, ref
  from objet
 where ref = :ref");
        $stmt->bindValue("ref", $ref);
        $stmt->execute();
        
        $row = $stmt->fetch();
        
        Logger::log("objet", "DEBUG", "Retrieves object id '$row[0]' from '$ref'");
        
        return new Objet($row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], $row[8], $row[9], $row[10], $row[0], $row[11], $row['letter'], $row["ref"]);
    }

    /**
     * Search an object from description
     * @param string $description
     * @return \classes\db\object\Objet[]
     */
    public static function findAll($ref, $description){
        $query = "
select pk, designation, prix, date_depot, date_vente, date_restitution,
    vendeur_pk, acheteur_pk, redacteurDepot_PK, redacteurVente_PK, redacteurRestitution_PK,
       id, letter, ref
  from objet
  where ref = :ref";

        // Explode description to search for all word using LIKE
        // TODO: update mysql to rely on FULLTEXT index
        $trimDescription = trim($description);
        if($trimDescription != " " && $trimDescription != null){
            $arr = explode(" ", preg_replace('/\s+/', ' ', trim($description)));
            $maxIdx = sizeof($arr);
            for ($idx = 0; $idx < $maxIdx; $idx++) {
                $query .= " OR UPPER(designation) like UPPER(:designation" . $idx. ") ";
            }
        } else{
            $maxIdx = 0;
        }

        $stmt = DB::getConn()->prepare($query);
        $stmt->bindValue("ref", $ref);
        for ($idx = 0; $idx < $maxIdx; $idx++) {
            $stmt->bindValue("designation".$idx, '%' . $arr[$idx]. '%');
        }
        
        $stmt->execute();
        
        $list = array();
        if($stmt->rowCount() > 0){
            foreach($stmt->fetchAll() as $row){
                $list[] = new Objet(
                    $row['designation'], $row['prix'], $row['vendeur_pk'], $row['acheteur_pk'],
                    $row['date_depot'], $row['date_vente'], $row['date_restitution'],
                    $row['redacteurDepot_PK'], $row['redacteurVente_PK'], $row['redacteurRestitution_PK'],
                    $row['pk'], $row['id'], $row['letter'], $row['ref']);
            }
        }
        return $list;
    }
    
    public function checkBeforeInsert(){
        return !empty($this->description) && !empty($this->prix);
    }
    
    /**
     * Returns all objects that exist in the database
     * @param string $orderBy
     * @return \classes\db\object\Objet[]
     */
    public static function getAll($orderBy = "asc"){
        $orderBy = $orderBy == "asc" ? "asc" : "desc";
        $stmt = DB::getConn()->prepare("select designation, prix, vendeur_PK, acheteur_PK, date_depot, date_vente, date_restitution, redacteurDepot_PK, redacteurVente_PK, redacteurRestitution_PK, PK, id, letter, ref from objet order by vendeur_PK asc, id asc");
        $stmt->bindParam(":orderBy", $orderBy);
        $stmt->execute();
        
        $list = array();
        if($stmt->rowCount() > 0){
            foreach($stmt->fetchAll() as $row){
                $list[] = new Objet($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], $row[8], $row[9], $row[10], $row[11], $row['letter'], $row['ref']);
            }
        }
        return $list;
    }
    
    /**
     * Retrieves all objects of the given vendor from the database
     * @param int $vendeur_pk The vendor's PK
     * @param type $orderBy
     * @return \classes\db\object\Objet[]
     */
    public static function getAllFromVendor($vendeur_pk, $orderBy = "asc"){
        $orderBy = $orderBy == "asc" ? "asc" : "desc";
        $stmt = DB::getConn()->prepare("select designation, prix, vendeur_PK, acheteur_PK, date_depot, date_vente, date_restitution, redacteurDepot_PK, redacteurVente_PK, redacteurRestitution_PK, PK, id, letter, ref from objet where vendeur_PK = :vendeur_PK order by PK ". $orderBy);
        $stmt->bindValue("vendeur_PK", $vendeur_pk);
        $stmt->execute();
        
        $list = array();
        if($stmt->rowCount() > 0){
            foreach($stmt->fetchAll() as $row){
                $list[] = new Objet($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], $row[8], $row[9], $row[10], $row[11], $row['letter'], $row['ref']);
            }
        }
        return $list;
    }
    
    /**
     * Remove the current object from the database
     */
    public function remove(){
        if($this->pk != null){
            $stmt = DB::getConn()->prepare("delete from objet where pk = :pk");
            $stmt->bindValue(":pk", $this->pk);
            $stmt->execute();
        }
    }
    
    /**
     * Save the current object in the database.
     * If the object has no pk set it will be inserted,
     * otherwise it will be updated.
     */
    public function save(){
        //If PK is null then INSERT
        if($this->pk == null  && $this->checkBeforeInsert()){
            $stmt = DB::getConn()->prepare("
insert into objet(designation, prix, vendeur_PK, id, date_depot, letter, ref)
           values (upper(:description), :prix, :vendeur_PK, :id, :date_depot, :letter, :ref)");
            $stmt->bindValue(":description", $this->description, PDO::PARAM_STR);
            $stmt->bindValue(":prix", $this->prix, PDO::PARAM_LOB);
            $stmt->bindValue(":vendeur_PK", $this->vendeur_PK, PDO::PARAM_INT);
            $stmt->bindValue(":date_depot", Date::format_to_sql($this->date_depot));
            $stmt->bindValue(":id", $this->id, PDO::PARAM_INT);
            $stmt->bindValue(":letter", $this->letter, PDO::PARAM_STR);
            $stmt->bindValue(":ref", $this->ref, PDO::PARAM_STR);
            
            $stmt->execute();
            //extract the id and set it to the current object
            $this->pk = DB::getConn()->lastInsertId();
            
            Logger::log("objet", "DEBUG", "Inserted object $this");
        }
        // Else : UPDATE
        else{
            $stmt = DB::getConn()->prepare("update objet set designation = upper(:description), prix = :prix, vendeur_PK = :vendeur_PK, date_baj = :date_baj, date_vente = :date_vente, date_restitution = :date_restitution, acheteur_PK = :acheteur_PK, redacteurDepot_PK = :redacteurDepot_PK, redacteurVente_PK = :redacteurVente_PK, redacteurRestitution_PK = :redacteurRestitution_PK where pk = :pk");
            $stmt->bindValue(":pk", $this->pk, PDO::PARAM_INT);
            $stmt->bindValue(":description", $this->description, PDO::PARAM_STR);
            $stmt->bindValue(":prix", $this->prix, PDO::PARAM_LOB);
            $stmt->bindValue(":vendeur_PK", $this->vendeur_PK, PDO::PARAM_INT);
            $stmt->bindValue(":date_baj", empty($this->date_baj) ? null : $this->date_baj);
            $stmt->bindValue(":date_vente", Date::format_to_sql($this->date_vente));
            $stmt->bindValue(":date_restitution", Date::format_to_sql($this->date_restitution));
            $stmt->bindValue(":acheteur_PK", $this->acheteur_PK, PDO::PARAM_STR);
            $stmt->bindValue(":redacteurDepot_PK", $this->redacteurDepot_PK, PDO::PARAM_STR);
            $stmt->bindValue(":redacteurVente_PK", $this->redacteurVente_PK, PDO::PARAM_STR);
            $stmt->bindValue(":redacteurRestitution_PK", $this->redacteurRestitution_PK, PDO::PARAM_STR);
            
            $stmt->execute();
        }
    }
    
    /**
     * Get the next object's ID to use
     * @return int
     */
    public function getNextId(){
        $stmt = DB::getConn()->prepare("select ifnull(max(id)+1, 1) from objet where vendeur_PK = :vendeur_PK");
        $stmt->bindValue(":vendeur_PK", $this->vendeur_PK);
        $stmt->execute();
        return $stmt->fetch()[0];
    }

    public static $EN_VENTE = "EN VENTE";
    public static $VENDU = "VENDU";
    public static $RESTITUE = "RESTITUÉ";
    public static $INVALIDE = "INVALIDE";
    
    /**
     * Get the actuel state of the object :
     * INVALIDE, EN VENTE, VENDU or RESTITUE
     * @return string
     */
    public function getState(){
        if($this->date_restitution != null && $this->date_restitution != ""){
            return Objet::$RESTITUE;
        }
       if($this->date_vente != null && $this->date_vente != ""){
           return Objet::$VENDU;
       }
       else if($this->date_depot != null && $this->date_depot != ""){
           return Objet::$EN_VENTE;
       }
       return Objet::$INVALIDE;
    }
    
    public function isDeposite(){
        return $this->getState() == Objet::$EN_VENTE || $this->getState() == Objet::$VENDU || $this->getState() == Objet::$RESTITUE;
    }
    
    public function isSale(){
        return $this->getState() == Objet::$VENDU || $this->getState() == Objet::$RESTITUE;
    }
    
    public function isRestitution(){
        return $this->getState() == Objet::$RESTITUE;
    }

    public function getDescription() {
        return $this->description;
    }
    
    function setDescription($description) {
        $this->description = $description;
    }
        
    function getPk() {
        return $this->pk;
    }

    function getPrix() {
        return $this->prix;
    }
    
    function setPrix($prix) {
        $this->prix = $prix;
    }

    function getDate_depot() {
        return $this->date_depot;
    }
    
    public function setDateDepot($date){
        $this->date_depot = $date;
    }

    function getDate_vente() {
        return $this->date_vente;
    }
    
    public function setDateVente($date){
        $this->date_vente = $date;
    }

    function getDate_restitution() {
        return $this->date_restitution;
    }
    
    public function setDateRestitution($date){
        $this->date_restitution = $date;
    }

    function getPk_vendeur() {
        return $this->vendeur_PK;
    }

    function getPk_acheteur() {
        return $this->acheteur_PK;
    }

    function getPk_redacteur_depot() {
        return $this->$redacteurDepot_PK;
    }

    function getPk_redacteur_vente() {
        return $this->redacteurVente_PK;
    }

    function getPk_redacteur_restitution() {
        return $this->redacteurRestitution_PK;
    }
    
    function getId(){
        return $this->id;
    }
    
    public function setId($id){
        $this->id = $id;
    }
    
    public function getLetter(){
        return $this->letter;
    }
    
    public function setLetter($letter){
        $this->letter = $letter;
    }

    public function __toString() {
        return "";
    }
    
    public function isEmpty(){
        return empty($this->pk) && empty($this->description) && empty($this->prix);
    }
    
    /**
     * Return the object reference with format : Axxx-yyy
     * Where x : object's seller PK and y : object's ID
     * @return string Object reference
     */
    public function getRef(){
//        return $this->letter . str_pad($this->getPk_vendeur(), Constants::$REF_OBJECT_SIZE, Constants::$REF_CHAR_COMPLETE, STR_PAD_LEFT)
//                . "-" . str_pad($this->getId(), Constants::$REF_OBJECT_SIZE, Constants::$REF_CHAR_COMPLETE, STR_PAD_LEFT);
        return $this->ref;
    }
}