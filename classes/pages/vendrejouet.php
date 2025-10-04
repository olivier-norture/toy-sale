<?php
namespace classes\pages;

use classes\db\object\Participant;
use classes\db\object\Objet;
use classes\db\object\Bill;
use classes\db\object\objetUtils;
use classes\pages\Page;
use classes\config\Constants;
use classes\utils\Logger;

class VendreJouet extends Page{

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
    private $hideButton;

    /**
     * 
     */
    function __construct(){
        parent::__construct();
        $this->acheteur = new Participant();
        $this->basket = new Basket();
        $this->hideButton = false;
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
        $this->basket = $this->getSession()->getBasket();
        $this->bill = $this->getSession()->getBill();
    }
    
    /**
     * 
     */
    protected function pageProcess(){
        $this->init();

        //If the user do an action
        if(!empty($_POST["action"])){
            if($_POST["action"] == "add" && !empty($_POST["ref"])){
                //Xxxx-Yyyy
                if(objetUtils::validateRef($_POST["ref"])){
                    $ref = objetUtils::formatRef($_POST["ref"]);
                    $this->addObject($ref);
                }
                else
                    $this->setErrorMessage ("La forme de la référence est incorrecte");
            }
            
            //If the action is "del-8", so we have to remove the objet with "pk = 8"
            elseif (substr($_POST["action"], 0, 3) == "del") {
                $id = substr($_POST["action"], 4);
                $this->removeObjet($id);
            }
            
            //If the action is "print"
            elseif($_POST["action"] == "saveAndPrint"){
                if($this->saveAndPrint())
                    header("Location: " . Constants::$PAGE_VENTE."?print");
                else if($this->isAdmin()){
                    $this->setErrorMessage($this->adminForceSell());
                }
            }
            elseif($_POST["action"] == "saveAndPrintUnchecked" && $this->isAdmin()){
                if($this->saveAndPrint(false))
                    header("Location: " . Constants::$PAGE_VENTE."?print");
            }
        }
        
        //If the user want to see an old Bill
        else if(!empty($_GET["facture"])){
            Logger::log("vendrejouet", "DEBUG", "Loading facture '" . $_GET["facture"] . "'");
            $billId = $_GET["facture"];
            $this->bill = Bill::search($billId);
            $this->setRedacteur(Participant::searchFromPk($this->bill->getRedactorPk()));
            $this->basket = $this->bill->getBasket();
            $this->getSession()->saveBasket($this->basket);
            $this->getSession()->saveBill($this->bill);
        }
        
        //Show a message if the current bill isn't active
        if(!$this->bill->isActive()){
            $bill = Bill::search($this->bill->getNewId());
            $this->setErrorMessage(Constants::$MESSAGE_OLD_BILL . " : '" . $bill->getRef() . "'.");
        }
    }
    
    /**
     * Add an object into the basket.
     * Updates the session's basket.
     * @param type $ref
     */
    private function addObject($ref){
        //Check if the object isn't already in the basket
        if($this->basket->contains(objetUtils::getObjectPkFromRef($ref))){
            $this->setErrorMessage("Cet objet est déjà dans le panier.");
            Logger::log("vendre_jouet", "DEBUG", "Basket contains object's id '$ref'");
            return;
        }
        Logger::log("vendre_jouet", "DEBUG", "Basket doesn't contains object's id '$ref'");
        
        $objet = Objet::searchRef($ref);
        if(!$objet->isEmpty()){
            
            //Check if the object isn't already selled
            if($objet->getPk_acheteur() > 0){
                $this->setErrorMessage(Constants::$MESSAGE_VENTE_OBJECT_ALREADY_SELLED);
                $this->setErrorMessage($objet->getRef());
                Logger::log("vendre_jouet", "DEBUG", "Trying to add object '$ref' which is alreay selled");
                return;
            }
            
            //Set the customer's id
            $objet->setAcheteur($this->getAcheteur()->getId());
            $this->basket->add($objet);
            $this->getSession()->saveBasket($this->basket);
        }
        else{
            $this->setErrorMessage(Constants::$MESSAGE_OBJECT_DOESNT_EXIST);
            $this->setErrorMessage($ref);
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
     * Generate the bill
     */
    private function saveAndPrint($checkBalance = true){
        $pay_cash = !empty($_POST["pay_cash"]) ? $_POST["pay_cash"] : 0;
        $pay_check = !empty($_POST["pay_check"]) ? $_POST["pay_check"] : 0;
        $pay_credit_card = !empty($_POST["pay_credit_card"]) ? $_POST["pay_credit_card"] : 0;
        $observations = !empty($_POST["observations"]) ? $_POST["observations"] : "";

        $oldID = $this->bill->getPk();
        $oldBill = Bill::search($oldID);
        
        //If there is an old bill and this one is inactive then we can't create a new bill
        //from this one. User must have to go on the last bill (security check).
        if($oldID != null && !$oldBill->isActive()){
            return false;
        }
        
        \classes\db\DB::getConn()->beginTransaction();
        
        //Create the bill
        $this->bill = new Bill($this->basket, Constants::$BILL_TYPE_VENTE, $pay_cash, $pay_check, $pay_credit_card, null, true, null, null, $this->acheteur->getId(), $this->getRedacteur()->getId());
        $this->bill->setLetter($this->getLetter());
        $this->bill->setObservations($observations);
        //check bill
        if($this->bill->checkSell($checkBalance == false && $this->isAdmin() == true ? false : true)){
            Logger::log("vendre_jouet", "DEBUG", "Bill check");
            //Save all objects in the database
            $date = \classes\utils\Date::getCurrentDateAndTime();
            foreach($this->basket->getAll() as $item){
                $item->setDateVente($date);
                $item->save();
            }

            //Save the bill
            $this->bill->save();
            //Save the bill in the session
            $this->getSession()->saveBill($this->bill);
            //Update the old bill if exist
            if(!empty($oldID)){
                Bill::updateBill($oldID, $this->bill->getPk());
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
     * Return a String witch represents an object stored in the customer's basket.
     * @param type $objet Objet
     * @return type String HTML code for a tab's row
     */
    private function renderRow($objet){
        return '<tr>
                <td><label>' . $objet->getRef() . '</label></td>
                <td><label>' . $objet->getDescription() . '</label></td>
                <td><label>' . $objet->getPrix() . '</label></td>
                <td class="notPrintable"><button name="action" type="submit" value="del-' . $objet->getPk() . '">Supprimer</button></td>
            </tr>';
    }
    
    /**
     * Print the customer's basket in LIFO mode.
     */
    public function renderTab(){
        for($i=$this->basket->size()-1; $i >=0 ; $i--){
            echo $this->renderRow($this->basket->getAll()[$i]);
        }
    }
    
    /**
     * Get the number of objects inside the basket
     * @return int number of objects
     */
    public function getTotalSize(){
        return $this->basket->size();
    }
    
    /**
     * Get the total price of the basket
     * @return int total price
     */
    public function getTotalPrice(){
        return $this->basket->getTotalPrice();
    }
    
    public function getBillId(){
        return $this->bill != null ? $this->bill->getRef() : 0;
    }
    
    public function getPayCash(){
        return $this->bill->getCach();
    }
    
    public function getPayCheck(){
        return $this->bill->getCheck();
    }

    public function getPayCreditCard(){
        return $this->bill->getCreditCard();
    }
    
    private function adminForceSell(){
        return '<form action="#" method="post">
                <button name="action" type="submit" value="saveAndPrintUnchecked">Forcer la vente</button>
              </form>';
    }
    
    public function showBillNumber(){
        if($this->bill != null && $this->bill->getPk() != null)
            echo '<label class="labelInfoTitle labelInfo">Facture N° : ' . $this->getBillId() . '</label>';
    }
    
    public function getObservations(){
        if($this->bill != null){
            return $this->bill->getObservations();
        }
    }
}

?>