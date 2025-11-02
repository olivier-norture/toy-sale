<?php
namespace classes\pages;

use classes\template\Session;
use classes\db\object\Objet;
use classes\db\object\Participant;
use classes\config\Constants;
use classes\utils\Logger;
use classes\db\object\Bill;
use classes\utils\Date;

class DepotJouet extends Page {

    /**
     * @var Participant
     */
    private $acheteur;
    /**
     * @var Basket
     */
    private $basket;
    /**
     * @var Bill
     */
    private $bill;

    public function __construct() {
        parent::__construct();
        $this->acheteur = new Participant();
        $this->basket = new Basket();
    }
    
    public function getBillDate(){
        return $this->bill->getDate();
    }

    /**
     * 
     * @return Participant
     */
    public function getAcheteur() {
        return $this->acheteur;
    }
    
    private function init() {
        $this->bill = $this->getSession()->getBill();
        $this->acheteur = $this->getSession()->getParticipant();
        $this->basket->addAll(Objet::getAllFromVendor($this->acheteur->getId(), "desc"));
    }
    
    protected function pageProcess() {
        $this->init();

        //If the user do an action
        if (!empty($_POST["action"])) {
            //If the action is "add" so we have to add the new object
            if ($_POST["action"] == "add" && !empty($_POST["description"]) && !empty($_POST["prix"])) {
                    $this->addObject();
                    header("Location: ".Constants::getPath(Constants::$PAGE_DEPOT));
            }
            //If the action is "del-8", so we have to remove the objet with "pk = 8"
            elseif (substr($_POST["action"], 0, 3) == "del") {
                $id = substr($_POST["action"], 4);
                $this->basket->get($id)->remove();
                $this->basket->del($id);
            }
            //If the action is "update-8", so we have to update the object with "pk = 8"
            elseif (substr($_POST["action"], 0, 6) == "update") {
                $id = substr($_POST["action"], 7);
                $objet = $this->basket->get($id);
                $objet->setDescription(strtoupper($_POST["object-description-".$id]));
                $objet->setPrix($_POST["object-prix-".$id]);
                $objet->setDateDepot(Date::getCurrentDateAndTime());
                $objet->save();
                $this->basket->update($objet);
            }
            elseif($_POST["action"] == "saveAndPrint"){
                if($this->saveAndPrint()){
                    header("Location: " . Constants::$PAGE_DEPOT."?print");
                }
            }
        }
        
        //If the user want to see an old bill
        elseif(!empty($_GET["facture"])){
            Logger::log("depotjouet", "DEBUG", "Loading facture '" . $_GET["facture"] . "'");
            $billId = $_GET["facture"];
            $this->bill = Bill::search($billId);
            $this->basket = $this->bill->getBasket();
            $this->getSession()->saveBill($this->bill);
        }
        
        //Show a message if the current bill isn't active
        if(!$this->bill->isActive()){
            $bill = Bill::search($this->bill->getNewId());
            $this->setErrorMessage(Constants::$MESSAGE_OLD_BILL . " : '" . $bill->getRef() . "'.");
        }
    }

    public function getEventDate() {
        echo "";
    }
    
    /**
     * 
     * @param Objet $objet
     * @return string
     */
    private function renderRow($objet){
        $html = '<tr>
                <td><label>' . $objet->getRef() . '</label></td>
                <td><textarea class="textarea" onkeydown="setTextareaHeight(this);" name="object-description-'. $objet->getPk() .'">'. $objet->getDescription() . '</textarea></td>
                <td><input type="text" name="object-prix-'. $objet->getPk() .'" value ="'. $objet->getPrix() .'" style="width: 100%; height: 100%;"/></td>';
        if($objet->getState() == Objet::$EN_VENTE){
            $html .= '
                <td class="notPrintable"><button class="edit" name="action" type="submit" value="update-'. $objet->getPk() .'">Modifier</button></td>
                <td class="notPrintable"><button class="edit" type="button" onclick="printLabel('. $objet->getPk() .')">Imprimer</button></td>
                <td class="notPrintable"><button name="action" type="submit" value="del-'. $objet->getPk() .'">Supprimer</button></td>
                ';
        }
        else{
            $html .= '
            <td class="notPrintable"></td>
            <td class="notPrintable"></td>
            <td class="notPrintable"></td>
            ';
        }
        $html .= '</tr>';
        return $html;
    }

    public function renderTab() {
        foreach($this->basket->getAll() as $item){
             echo $this->renderRow($item);
        }
    }
    
    public function getLetter(){
        if($this->basket->size() > 0){
            return $this->basket->getAll()[0]->getLetter();
        }
        return parent::getLetter();
    }

    public function addObject() {
        $objet = new Objet(strtoupper($_POST["description"]), str_replace(",", ".", $_POST["prix"]), $this->getAcheteur()->getId());
        $objet->setLetter($this->getLetter());
        $objet->setId($objet->getNextId());
        $objet->setRef($this->acheteur, $this->getLetter());
        $objet->setDateDepot(Date::getCurrentDateAndTime());
        
        $objet->save();
        $this->basket->add($objet);
    }
    
    public function getTotalSize(){
        return $this->basket->size();
    }
    
    public function saveAndPrint(){
        $oldID = $this->bill->getPk();
        $oldBill = Bill::search($oldID);
        
        //If there is an old bill and this one is inactive then we can't create a new bill
        //from this one. User must have to go on the last bill (security check).
        if($oldID != null && !$oldBill->isActive()){
            return false;
        }
        
        \classes\db\DB::getConn()->beginTransaction();
        
        //Create the new bill
        $this->bill = new Bill($this->basket, Constants::$BILL_TYPE_DEPOT, 0, 0, 0, null, true, null, null, $this->getAcheteur()->getId(), $this->getRedacteur()->getId());
        $this->bill->setLetter($this->getLetter());
        //Check the bill
        if($this->bill->checkSell(false)){
            Logger::log("depotjouet", "DEBUG", "Bill checked");
            $date = Date::getCurrentDateAndTime();
            foreach($this->basket->getAll() as $object){
                if($object->getState() == Objet::$EN_VENTE){
                    $object->setDateDepot($date);
                    $object->save();
                }
            }
            
            //Save the bill
            $this->bill->save();
            $this->getSession()->saveBill($this->bill);
            //Update the old bill if exist
            if(!empty($oldID)){
                Bill::updateBill($oldID, $this->bill->getPk());
            }
            \classes\db\DB::getConn()->commit();
            return true;
        }
        else{
            Logger::log("depotjoue", "DEBUG", "Bill not checked '" . $this->bill->getMessage() ."'");
            $this->setErrorMessage($this->bill->getMessage());
            \classes\db\DB::getConn()->rollBack();
            return false;
        }
        
    }
    
    public function getBillId(){
        return $this->bill != null ? $this->bill->getRef() : 0;
    }
    
    public function showBillNumber(){
        if($this->bill != null && $this->bill->getPk() != null)
            echo '<label class="labelInfoTitle labelInfo">Facture N° : ' . $this->getBillId() . '</label>';
    }
    
}
