<?php
namespace classes\db\object;

use classes\pages\Basket;
use classes\db\DB;
use classes\config\Constants;
use classes\utils\Logger;

class Bill {
    private $pk;
    private $type;
    /**
     * @var Basket
     */
    private $basket;
    private $cash;
    private $check;
    private $creditCard;
    private $message;
    private $active;
    private $newId;
    private $date;
    private $customerPk;
    private $redactorPk;
    private $tax;
    private $letter;
    private $observations;
    
    /**
     * Create a new Bill
     * @param Basket $basket
     * @param double $cash
     * @param double $check
     * @param double $creditCard
     * @param int $pk
     * @param bool $active
     * @param int $newId
     * @param int $customerPk
     * @param int redactorPk
     */
    public function __construct($basket, $type, $cash, $check, $creditCard, $pk = null, $active = true, $newId = null, $date = null, $customerPk = null, $redactorPk = null, $tax = 20, $letter = null, $observation = null) {
        $this->pk = $pk;
        $this->type = $type;
        $this->basket = $basket;
        $this->cash = $cash;
        $this->check = $check;
        $this->creditCard = $creditCard;
        $this->active = $active;
        $this->newId = $newId;
        $this->date = $date;
        $this->customerPk = $customerPk;
        $this->redactorPk = $redactorPk;
        $this->tax = $tax;
        $this->letter = $letter == null ? Constants::$DEFAULT_LETTER : $letter;
        $this->observations = $observation;
    }
    
    /**
     * Get the bill's ID
     * @return int
     */
    public function getPk(){
        return $this->pk;
    }
    
    public function getCach(){
        return $this->cash;
    }
    
    public function getCheck(){
        return $this->check;
    }

    public function getCreditCard(){
        return $this->creditCard;
    }
    
    public function getBasket(){
        return $this->basket;
    }
    
    public function isActive(){
        return $this->active;
    }
    
    public function getNewId(){
        return $this->newId;
    }
    
    /**
     * Check if the sell can be done.
     * For exemple, check if all object's aren't already be selled.
     * @return boolean True if the sell can be done, otherwise false
     */
    public function checkSell($checkBalance = true){
        //Check if the bill is active
        if($this->active != true){
            $this->message = Constants::$MESSAGE_VENTE_BILL_INACTIVE;
            return false;
        }
        
        //Check if an object is already selled
        foreach($this->basket->getAll() as $objet){
            if ($objet->getPk_acheteur() < 0) {
                $this->message = Constants::$MESSAGE_VENTE_SOME_OBJECTS_ALREADY_SELLED;
                return false;
            }
        }
        
        //Check if we get enough money
        if($checkBalance){
            if($this->basket->getTotalPrice() > $this->cash + $this->check + $this->creditCard){
                $this->message = Constants::$MESSAGE_VENTE_NOT_ENOUGH_MONEY;
                return false;
            }
        }
        
        //Check if all field are set
        if (!empty($this->redactorPk) && !empty($this->customerPk)){
            $this->message = Constants::$MESSAGE_VENTE_VENDOR_PK;
        }
        
        return true;
    }
    
    /**
     * If the method checkSell return false you can use this method to retrieves
     * an erreor message.
     * @return string
     */
    public function getMessage(){
        return $this->message;
    }
    
    /**
     * Save the bill in the database
     */
    public function save(){
        $this->insert();
    }
    
    /**
     * Insert the current bill into the database
     */
    private function insert(){
        if($this->date == null)
            $this->date = \classes\utils\Date::getCurrentDateAndTime ();
        
        //Create the bill
        $stmtBill = DB::getConn()->prepare("insert into bill(`type`, `cash`, `check`, `creditCard`, `total`, `date`, customer_pk, redactor_pk, tax, letter, observations) values(:type, :cash, :check, :creditCard, :total, STR_TO_DATE(:date, '%d/%m/%Y %T'), :customer_pk, :redactor_pk, :tax, :letter, UPPER(:observations))");
        $stmtBill->bindValue(":type", $this->type);
        $stmtBill->bindValue(":cash", $this->cash);
        $stmtBill->bindValue(":check", $this->check);
        $stmtBill->bindValue(":creditCard", $this->creditCard);
        $stmtBill->bindValue(":total", $this->basket->getTotalPrice());
        $stmtBill->bindValue(":customer_pk", $this->customerPk);
        $stmtBill->bindValue(":redactor_pk", $this->redactorPk);
        $stmtBill->bindValue(":tax", $this->tax);
        $stmtBill->bindValue(":letter", $this->letter);
        $stmtBill->bindValue(":date", $this->date);
        $stmtBill->bindValue(":observations", $this->observations);
        
        $stmtBill->execute();
        $this->pk = DB::getConn()->lastInsertId();
        
        //Attach all objects to the bill
        $stmtObj = DB::getConn()->prepare("insert into bill_objects(bill_id, object_id) values(:bill_id, :object_id)");
        foreach($this->basket->getAll() as $object){
            $stmtObj->bindValue(":bill_id", $this->pk);
            $stmtObj->bindValue(":object_id", $object->getPk());
            
            $stmtObj->execute();
        }
        
        Logger::log("bill", "debug", "Bill id : '". $this->pk ."' created !");
    }
    
    /**
     * 
     * @param type $ref
     * @return \classes\db\object\Bill|null
     */
    public static function search($ref){
        //Retrieves the bill
        $stmt = DB::getConn()->prepare("select id, type, cash, `check`, creditCard, total, active, new_id, DATE_FORMAT(`date`, '%d/%m/%Y %T') as date, customer_pk, redactor_pk, tax, letter, observations from bill where id = :id");
        $stmt->bindValue(":id", $ref);
        $stmt->execute();
        $billInfo = $stmt->fetch();

        if (empty($billInfo)) {
            return null;
        }
        
        //Retrieves the bill's basket
        $basket = new Basket();
        $stmtList = DB::getConn()->prepare("select id, bill_id, object_id from bill_objects where bill_id = :bill_id");
        $stmtList->bindParam(":bill_id", $ref);
        $stmtList->execute();
        foreach($stmtList->fetchAll() as $row){
            $basket->add(Objet::searchPk($row["object_id"]));
        }
        
        return new Bill($basket, $billInfo["type"], $billInfo["cash"], $billInfo["check"], $billInfo["creditCard"], $billInfo["id"], $billInfo["active"], $billInfo["new_id"], $billInfo["date"], $billInfo["customer_pk"], $billInfo["redactor_pk"], $billInfo ["tax"], $billInfo["letter"], $billInfo["observations"]);
    }
    
    /**
     * Retrieve the last active bill for the given object
     * @param int $objectID The object's ID to use
     * @return Bill|null
     */
    public static function searchLastActiveFromObjet($objectID, $type){
        $stmt = DB::getConn()->prepare("select max(bil.id) as id
                                          from bill bil
                                          join bill_objects obj on (bil.id = obj.bill_id)
                                         where bil.active = true
                                           and obj.object_id = :object_id
                                           and bil.type = :type");
        $stmt->bindValue(":object_id", $objectID);
        $stmt->bindValue(":type", $type);
        $stmt->execute();
        $result = $stmt->fetch();
        if (empty($result) || empty($result['id'])) {
            return null;
        }
        return Bill::search($result["id"]);
    }
    
    public static function updateBill($oldBillID, $newBillID){
        $stmt = DB::getConn()->prepare("update bill
                                           set active = false,
                                               new_id = :new_id
                                         where id     = :previous_id");
        $stmt->bindParam("new_id", $newBillID);
        $stmt->bindParam("previous_id", $oldBillID);
        $stmt->execute();
        
        $oldBill = Bill::search($oldBillID);
        $newBill = Bill::search($newBillID);
        
        foreach($oldBill->basket->getAll() as $object){
            if(!$newBill->basket->contains($object->getPk())){
                $object->setAcheteur(null);
                $object->setRedacteurVente(null);
                $object->save();
            }
        }
    }
    
    /**
     * Retrieves all Bills from the database
     * @param string $orderBy orderBy on id, default asc
     * @return Bill[]
     */
    public static function getAll($orderBy = "asc"){
        $stmt = DB::getConn()->prepare("select id from bill order by id desc");
        $stmt->bindValue(":orderBy", $orderBy);
        $stmt->execute();
        
        $bills = array();
        foreach($stmt->fetchAll() as $row){
            $bills[] = Bill::search($row["id"]);
        }
        
        return $bills;
    }

    public function setDate($date){
        $this->date = $date;
    }
    
    public function getDate() {
        return $this->date;
    }
    
    public function setCustomerPk($customerPk){
        $this->customerPk = $customerPk;
    }

    public function getCustomerPk() {
        return $this->customerPk;
    }
    
    public function setRedactorPk($redactorPk){
        $this->redactorPk = $redactorPk;
    }

    public function getRedactorPk() {
        return $this->redactorPk;
    }

    public function getTotal() {
        return $this->basket->getTotalPrice();
    }

    public function getNbJouets() {
        return count($this->basket->getAll());
    }
    
    public function getType(){
        return $this->type;
    }
    
    public function setType($type){
        $this->type = $type;
    }

    public function getTax(){
        return $this->tax;
    }
    
    public function setTax($tax){
        $this->tax = $tax;
    }
    
    public function getLetter(){
        return $this->letter;
    }
    
    public function setLetter($letter){
        $this->letter = $letter;
    }
    
    public function setObservations($observations){
        $this->observations = strtoupper($observations);
    }
    
    public function getObservations(){
        return $this->observations;
    }
    
    /**
     * Retrieve the bill's ref like "D20171119001".
     */
    public function getRef(){
        $firstLetter = "";
        switch ($this->type){
            case Constants::$BILL_TYPE_DEPOT:
                $firstLetter = "D";
                break;
            case Constants::$BILL_TYPE_VENTE:
                $firstLetter = "V";
                break;
            case Constants::$BILL_TYPE_RESTITUTION:
                $firstLetter = "R";
                break;
        }
        
        return $firstLetter . $this->letter . str_replace('/', '', substr($this->date, 0, 10)) . str_pad($this->getPk(), Constants::$REF_BILL_SIZE, Constants::$REF_CHAR_COMPLETE, STR_PAD_LEFT);;
    }
}
