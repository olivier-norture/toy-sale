<?php
namespace classes\pages;

use classes\db\object\Participant;
use classes\config\Constants;
use classes\db\object\Bill;

class RechercheFacture extends Page{
    
    public function __construct() {
        parent::__construct();
    }
    
//    private function getUrlForDirectPrint($bill){
//        switch ($bill->getType()){
//            case Constants::$BILL_TYPE_DEPOT:
//                $page = Constants::$PAGE_DEPOT;
//                break;
//            case Constants::$BILL_TYPE_VENTE:
//                $page = Constants::$PAGE_VENTE;
//                break;
//            case Constants::$BILL_TYPE_RESTITUTION:
//                $page = Constants::$PAGE_RESTITUTION;
//                break;
//        }
//        $this->getSession()->saveParticipant(Participant::searchFromPk($bill->getCustomerPk()));
//        return Constants::getPath($page) . "?facture=" . $bill->getPk() . "&print";
//    }

    protected function pageProcess() {
        if(!empty($_POST["facture"])){
            $bill = Bill::search($_POST["facture"]);
            
            $page = "";
            switch ($bill->getType()){
                case Constants::$BILL_TYPE_DEPOT:
                    $page = Constants::$PAGE_DEPOT;
                    break;
                case Constants::$BILL_TYPE_VENTE:
                    $page = Constants::$PAGE_VENTE;
                    break;
                case Constants::$BILL_TYPE_RESTITUTION:
                    $page = Constants::$PAGE_RESTITUTION;
                    break;
            }
            
            $this->getSession()->saveParticipant(Participant::searchFromPk($bill->getCustomerPk()));
            header("Location: " . Constants::getPath($page) . "?facture=".$bill->getPk());
        }
    }
    
    public function renderTab(){
        $bills = \classes\db\object\Bill::getAll("desc");
        $html = "";
        foreach ($bills as $bill){
            $html .= $this->renderRow($bill);
        }
        echo $html;
    }
    
    /**
     * 
     * @param \classes\db\object\Bill $bill
     */
    private function renderRow($bill){
        $customer = Participant::searchFromPk($bill->getCustomerPk());
        $redactor = Participant::searchFromPk($bill->getRedactorPk());
        
        return '<tr>
                    <td> <label>' . $bill->getRef() .'</label> </td>
                    <td> <label>' . $bill->getType() .'</label> </td>
                    <td> <label>' . $bill->getDate() . '</label> </td>
                    <td> <label>' . $customer->getNom() . " " . $customer->getPnom() . '</label> </td>
                    <td> <label>' . $redactor->getNom() . " " . $redactor->getPnom() . '</label> </td>
                    <td> <label>' . $bill->getTotal() . '</label> </td>
                    <td> <label>' . $bill->getNbJouets() . '</label> </td>
                    <td> <button name="facture" type="submit" value="' . $bill->getPk() . '">Détails</button> </td>
                </tr>';
    }
    

}
