<?php
namespace classes\pages;

use classes\pages\Basket;
use classes\db\object\Participant;
use classes\db\object\Objet;
use classes\pages\Page;
use classes\config\Constants;
use classes\db\object\Bill;
use classes\utils\Logger;

/**
 * 
 */
class RestitutionJouet extends Page{

    /**
     * Customer.
     * @var type Participant
     */
    private $acheteur;
    /**
     * List of selled objects.
     * @var Basket 
     */
    private $basketSelled;
    /**
     * List of unselled objects.
     * @var Basket 
     */
    private $basketUnselled;
    /**
     * @var Bill
     */
    private $bill;

    function __construct(){
        parent::__construct();
        $this->acheteur = new Participant();
        $this->basketSelled = new Basket();
        $this->basketUnselled = new Basket();
    }

    /**
     * Get the customer.
     * @return type Participant
     */
    public function getAcheteur(){
        return $this->acheteur;
    }
    
    /**
     * Retrieves the customer and the customer's basket from the session
     */
    private function init(){
        $this->acheteur = $this->getSession()->getParticipant();
        $this->bill = $this->getSession()->getBill();
        $this->initBasket();
    }
    
    /**
     * 
     */
    protected function pageProcess(){
        if (!empty($_POST['action']) && $_POST['action'] == 'search_participant' && !empty($_POST['ref_participant'])) {
            $participant = \classes\db\object\Participant::searchByRef($_POST['ref_participant']);
            if (!$participant->isEmpty()) {
                $this->getSession()->saveParticipant($participant);
                $this->getSession()->saveBill(new \classes\db\object\Bill(null, null, 0, 0, 0));
                // After changing participant, we redirect to the same page to avoid form resubmission on refresh
                header("Location: " . \classes\config\Constants::$PAGE_RESTITUTION);
                exit();
            } else {
                $this->setErrorMessage("Aucun participant trouvé avec cette référence.");
            }
        }

        $this->init();
        
        //If the user do an action
        if(!empty($_POST["action"]) && $this->bill->isActive()){
            if($_POST["action"] == "add" && !empty($_POST["ref"])){
                $this->addObject($_POST["ref"]);
            }
            
            //If the action is "del-8", so we have to remove the objet with "pk = 8"
            elseif (substr($_POST["action"], 0, 3) == "del") {
                $id = substr($_POST["action"], 4);
                $this->removeObjet($id);
            }
            
            //If the action is "print"
            elseif($_POST["action"] == "saveAndPrint"){
                if($this->saveAndPrint())
                    header("Location: ". Constants::$PAGE_RESTITUTION."?print");
            }
            
            //Change the tax value
            elseif($_POST["action"] == "tax"){
                $this->bill->setTax($_POST["tax"]);
                $this->getSession()->saveBill($this->bill);
            }
        } 
        //If the user want to see an old Bill
        else if(!empty($_GET["facture"])){
            Logger::log("restitutionjouet", "DEBUG", "Loading facture '" . $_GET["facture"] . "'");
            $this->bill = Bill::search($_GET["facture"]);
            $this->setRedacteur(Participant::searchFromPk($this->bill->getRedactorPk()));
            $this->basket = $this->bill->getBasket();
            $this->getSession()->saveBill($this->bill);
        }
        
        //Show a message if the current bill isn't active
        if(!$this->bill->isActive()){
            $bill = Bill::search($this->bill->getNewId());
            if ($bill) {
                $this->setErrorMessage(Constants::$MESSAGE_OLD_BILL . " : '" . $bill->getRef() . "'.");
            }
        }
    }
    
    private function initBasket(){
        if ($this->bill->getBasket() == null) {
            $objets = Objet::getAllFromVendor($this->acheteur->getId());
        } else{
            $objets = $this->bill->getBasket()->getAll();
        }

        foreach($objets as $objet){
                if($objet->getPk_acheteur() > 0){
                    $this->basketSelled->add($objet);
                }
                else{
                    $this->basketUnselled->add($objet);
                }
            }
    }
    
    /**
     * Add an object to the list.
     * Updates the session's list.
     * @param type $id
     */
    private function addObject($id){
        if($this->basket->contains($id)){
            return;
        }
        
        $objet = Objet::searchPk($id);
        if(!$objet->isEmpty()){
            //Set the customer's id
            $objet->setAcheteur($this->getAcheteur()->getId());
            $this->basket->add($objet);
            $this->getSession()->saveBasket($this->basket);
        }
    }
    
    /**
     * Removes an object from the list.
     * Updates the session's list.
     * @param type $id
     */
    private function removeObjet($id){
        $this->basket->del($id);
        $this->getSession()->saveBasket($this->basket);
    }
    
    /**
     * Save the current list into the database.
     * Also print all objects in the current basket.
     */
    private function saveAndPrint(){
        $oldBillId = $this->bill->getPk();
        $oldBill = Bill::search($oldBillId);
        
        //If there is an old bill and this one is inactive then we can't create a new bill
        //from this one. Use must have to go on the last bill (security check).
        if($oldBillId != null && !$oldBill->isActive()){
            return false;
        }
        
        \classes\db\DB::getConn()->beginTransaction();
        
        //Set the date_restitution for all objects
        $basket = new Basket();
        $date = \classes\utils\Date::getCurrentDateAndTime();
        foreach($this->basketUnselled->getAll() as $objet){
            $objet->setDateRestitution($date);
            $objet->save();
            $basket->add($objet);
        }
        foreach($this->basketSelled->getAll() as $objet){
            $objet->setDateRestitution($date);
            $objet->save();
            $basket->add($objet);
        }
        
        //Create a new Bill
        $this->bill = new Bill($basket, Constants::$BILL_TYPE_RESTITUTION, 0, 0, 0, null, true, null, null, $this->acheteur->getId(), $this->getRedacteur()->getId(), $this->getTaxPercent());
        $this->bill->setLetter($this->getLetter());
        //Check the bill
        if($this->bill->checkSell(false)){
            \classes\utils\Logger::log("restitution_jouet", "DEBUG", "Bill checked");
            //Save the bill
            $this->bill->save();
            $this->getSession()->saveBill($this->bill);
            if(!empty($oldBillId)){
                Bill::updateBill($oldBillId, $this->bill->getPk());
            }
            \classes\db\DB::getConn()->commit();
            return true;
        }
        else{
            Logger::log("vendre_jouet", "DEBUG", "Bill not check : '".$this->bill->getMessage()."'");
            $this->setErrorMessage($this->bill->getMessage());
            \classes\db\DB::getConn()->rollBack();
            return false;
        }
    }
    
    /**
     * 
     * @param Basket $basket
     */
    private function getTotal($basket){
        $sum = 0;
        foreach($basket->getAll() as $item){
            $sum += $item->getPrix();
        }
        return $sum;
    }
    
    public function getTotalSelled(){
        return $this->getTotal($this->basketSelled);
    }
    
    public function getTotalUnselled(){
        return $this->getTotal($this->basketUnselled);
    }
    
    public function getNbItemSelled(){
        return $this->basketSelled->size();
    }
    
    public function getNbItemUnselled(){
        return $this->basketUnselled->size();
    }
    
    public function getTaxPercent(){
        return $this->bill->getTax();
    }
    
    public function getTotalSelledMinusTax(){
        return $this->getTotalSelled() * (1-($this->getTaxPercent())/100);
    }
    
    /**
     * Return a String witch represents an object stored in the customer's basket.
     * @param Objet $objet Objet to render
     * @param boolean $showPrice 
     * @return type String HTML code for a tab's row
     */
    private function renderRow($objet, $showPrice = true){
        $html = '<tr>
                <td><label>' . $objet->getRef() . '</label></td>
                <td><label>' . $objet->getDescription() . '</label></td>';
        
        if($showPrice){
            $html .= '<td><label>' . $objet->getPrix() . '</label></td>';
        }
        return $html . '</tr>';
    }
    
    /**
     * Print the customer's basket.
     */
    public function renderTabSelled(){
        foreach($this->basketSelled->getAll() as $item){
            echo $this->renderRow($item);
        }
    }
    
    /**
     * Print the customer's basket.
     */
    public function renderTabUnselled(){
        foreach($this->basketUnselled->getAll() as $item){
            echo $this->renderRow($item, false);
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

?>