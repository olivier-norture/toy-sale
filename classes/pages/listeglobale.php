<?php
namespace classes\pages;

use classes\db\object\Bill;
use classes\config\Constants;
use classes\db\object\Participant;

class ListeGlobale extends Page{
    protected function pageProcess() {
       if(!empty($_POST["factureDeposite"])){
            $bill = Bill::search($_POST["factureDeposite"]);
            $this->getSession()->saveParticipant(Participant::searchFromPk($bill->getCustomerPk()));
            header("Location: " . Constants::getPath(Constants::$PAGE_DEPOT) . "?facture=".$bill->getPk());
        }
       if(!empty($_POST["factureSale"])){
            $bill = Bill::search($_POST["factureSale"]);
            $this->getSession()->saveParticipant(Participant::searchFromPk($bill->getCustomerPk()));
            header("Location: " . Constants::getPath(Constants::$PAGE_VENTE) . "?facture=".$bill->getPk());
        }
       if(!empty($_POST["factureRestitution"])){
            $bill = Bill::search($_POST["factureRestitution"]);
            $this->getSession()->saveParticipant(Participant::searchFromPk($bill->getCustomerPk()));
            header("Location: " . Constants::getPath(Constants::$PAGE_RESTITUTION) . "?facture=".$bill->getPk());
        }
    }

    public function renderTab(){
        $html = "";
        foreach(\classes\db\object\Objet::getAll() as $objet){
            $html .= $this->renderRow($objet);
        }
        echo $html;
    }
    
    /**
     * 
     * @param \classes\db\object\Objet $objet
     */
    private function renderRow($objet){
        $html = '<tr>
                    <td> <label>' . $objet->getRef() .'</label> </td>
                    <td> <label>' . $objet->getDescription() . '</label> </td>
                    <td> <label>' . $objet->getPrix() . '</label> </td>
                    <td> <label>' . $objet->getState() . '</label> </td>';
           
        $billD = Bill::searchLastActiveFromObjet($objet->getPk(), Constants::$BILL_TYPE_DEPOT);
        $billV = Bill::searchLastActiveFromObjet($objet->getPk(), Constants::$BILL_TYPE_VENTE);
        $billR = Bill::searchLastActiveFromObjet($objet->getPk(), Constants::$BILL_TYPE_RESTITUTION);
        if($billD){
            $html .= '<td> <button name="factureDeposite" type="submit" value="' . $billD->getPk() . '">Détails</button> </td>';
        } else{
            $html .= '<td> </td>';
        }
        if($billV){
            $html .= '<td> <button name="factureSale" type="submit" value="' . $billV->getPk() . '">Détails</button> </td>';
        } else{
            $html .= '<td> </td>';
        }
        if($billR){
            $html .= '<td> <button name="factureRestitution" type="submit" value="' . $billR->getPk() . '">Détails</button> </td>';
        } else{
            $html .= '<td> </td>';
        }
                $html .= '</tr>';
        return $html;
    }
}
