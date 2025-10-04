<?php
namespace classes\pages;

use classes\db\object\objetUtils;
use classes\db\object\Objet;
use classes\db\object\Participant;
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
            <td style="width: 100%;">' . $object->getDescription() . '</td>
            <td>' . $object->getPrix() . '</td>
            <td>' . $object->getState() . '</td>
            <td>' . $seller->getNom() . '</td>
            <td>' . $seller->getPnom() . '</td>
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