<?php
namespace classes\pages;

use classes\db\object\objetUtils;
use classes\db\object\Objet;
use classes\db\object\Participant;
use classes\db\object\Bill;
use classes\config\Constants;

class Recherche extends Page
{
    private $object;
    private $vendor;
    public $searchRef = "";
    public $searchDescription = "";
    public $objectList = [];

    protected function pageProcess()
    {
        //Open one of the object's bills (depot, vente or restitution) in its own page
        if (!empty($_POST["bill"]) && $this->isAdmin()) {
            $this->openBill($_POST["bill"]);
        }

        if (isset($_POST["action"])) {

            if ($_POST["action"] == "search") {
                $this->searchRef = objetUtils::validateRef($_POST["ref"]) ? objetUtils::formatRef($_POST["ref"]) : "";
                $this->searchDescription = $_POST["description"];
                $this->objectList = $this->findAll($this->searchRef, $this->searchDescription);
            } else if ($_POST["action"] == "details") {
                $ref = $_POST["ref"];
                $this->findByRefAndSetResult($ref);
                $this->getSession()->saveParticipant($this->vendor);
                header("Location: " . Constants::getPath(Constants::$PAGE_DEPOT));
            }
        }
    }

    /**
     * Go to the page of the given bill (same behavior as the "Détails" button of the bills' search)
     */
    private function openBill($billId)
    {
        $bill = Bill::search($billId);
        if ($bill == null) {
            return;
        }

        switch ($bill->getType()) {
            case Constants::$BILL_TYPE_DEPOT:
                $page = Constants::$PAGE_DEPOT;
                break;
            case Constants::$BILL_TYPE_VENTE:
                $page = Constants::$PAGE_VENTE;
                break;
            case Constants::$BILL_TYPE_RESTITUTION:
                $page = Constants::$PAGE_RESTITUTION;
                break;
            default:
                return;
        }

        $this->getSession()->saveParticipant(Participant::searchFromPk($bill->getCustomerPk()));
        header("Location: " . Constants::getPath($page) . "?facture=" . $bill->getPk());
        die;
    }

    /**
     * Buttons to open the latest active bill of the object for each type (depot, vente, restitution)
     */
    private function renderBills($object)
    {
        $types = array(
            Constants::$BILL_TYPE_DEPOT => "Dépôt",
            Constants::$BILL_TYPE_VENTE => "Vente",
            Constants::$BILL_TYPE_RESTITUTION => "Restitution",
        );

        $html = "";
        foreach ($types as $type => $label) {
            $bill = Bill::searchLastActiveFromObjet($object->getPk(), $type);
            if ($bill != null) {
                $html .= '<button type="submit" name="bill" value="' . $bill->getPk() . '" title="' . $bill->getRef() . '" style="display: block; white-space: nowrap; margin: 2px 0;">' . $label . ' ' . $bill->getRef() . '</button>';
            }
        }
        return $html;
    }

    private function findAll($ref, $description)
    {
        $formatRef = objetUtils::validateRef($ref) ? objetUtils::formatRef($ref) : "";
        return Objet::findAll($formatRef, $description);
    }

    public function renderSearchResults()
    {
        $html = "";
        foreach ($this->objectList as $object) {
            $seller = Participant::searchFromPk($object->getPk_vendeur());

            $html .= '
            <tr>
            <form action="#" method="post" id="contact_form">
            <input type="text" name="ref" style="display: none" value="' . $object->getRef() . '"/>
            <td>' . $object->getRef() . '</td>
            <td style="width: 30%;">' . $object->getDescription() . '</td>
            <td>' . $object->getPrix() . '</td>
            <td>' . $object->getState() . '</td>
            <td>' . $seller->getNom() . '</td>
            <td>' . $seller->getPnom() . '</td>
            <td>' . ($this->isAdmin() ? $this->renderBills($object) : '') . '</td>
            <td>';
            if($object->getState() == Objet::$EN_VENTE && $this->isAdmin()){
                $html .= '<button type="submit" name="action" value="details">Modifier</button>';
            }
            $html .= '</td>
            </form>
            </tr>
            ';
        }

        return $html;
    }

    private function findByRefAndSetResult($ref)
    {
        if (objetUtils::validateRef($ref)) {
            $ref = objetUtils::formatRef($ref);
            $this->object = Objet::searchRef($ref);
            $this->vendor = Participant::searchFromPk($this->object->getPk_vendeur());
        }
    }

    /**
     * @return Objet
     */
    public function getObjet()
    {
        return $this->object;
    }

    /**
     * @return Participant
     */
    public function getVendor()
    {
        return $this->vendor;
    }
}