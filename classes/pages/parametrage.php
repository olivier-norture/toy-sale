<?php
namespace classes\pages;

use classes\db\object\Participant;
use classes\pages\Page;
use classes\config\Constants;
use classes\db\object\User;

class Parametrage extends Page {

    /**
     * @var Partiticpant
     */
    private $user;
    private $users;

    public function __construct() {
        parent::__construct();
        $this->user = new User();
        $this->participantFind = false;
        $this->showWarning = false;
        $this->basket = new Basket();
        $this->users = array();
    }

    /**
     * 
     * @return Participant
     */
    public function getUser() {
        return $this->user;
    }

    private function init() {
        $this->user = $this->getSession()->getUser();
        $this->basket->addAll(User::getAll("Asc"));
        $this->users = User::getAll();
    }
    
    /**
     * Search for a Participant
     */
    private function search() {
        $this->vendeur = $this->vendeur->searchPk($_POST["nom"], $_POST["prenom"]);
        //If the search return nothing, juste create a new Participant with the first and last name to set their values in form
        if($this->vendeur->isEmpty()){
            $this->vendeur = new Participant("", $_POST["nom"], $_POST["prenom"]);
        }
        $this->getSession()->saveParticipant($this->vendeur);
    }

    /**
     * Add a new Participant
     */
    private function add() {
        $adresse = !empty($_POST['adresse']) ? $_POST["adresse"] : "";
        $code_postal = !empty($_POST['code_postal']) ? $_POST["code_postal"] : "";
        $ville = !empty($_POST['ville']) ? $_POST["ville"] : "";
        $email = !empty($_POST['email']) ? $_POST["email"] : "";
        
        $this->vendeur = new Participant(null, $_POST["nom"], $_POST["prenom"], $adresse, $code_postal, $ville, $email, $_POST["tel"]);

        if ($this->vendeur->checkBeforeInsert()) {
            $this->vendeur->save();
            $this->getSession()->saveParticipant($this->vendeur->searchPk($this->vendeur->getNom(), $this->vendeur->getPnom()));
        }
    }

    public function pageProcess() {
        $this->init();

        if (isset($_POST["action"])) {
            //If the action is "update-8", so we have to update the user with "pk = 8"
            if(substr($_POST["action"], 0, 6) == "update"){
                //Extract the id
                $id = substr($_POST["action"], 7);
                
                //Extract all values
                $isAdmin = isset($_POST["isAdmin".$id]) ? 1 : 0;
                $isDepot = isset($_POST["isDepot".$id]) ? 1 : 0;
                $isVente = isset($_POST["isVente".$id]) ? 1 : 0;
                $isRestitution = isset($_POST["isRestitution".$id]) ? 1 : 0;
                
                //Update the user
                $user = User::get($id);
                $user->setIsAdmin($isAdmin);
                $user->setIsDepot($isDepot);
                $user->setIsVente($isVente);
                $user->setIsRestitution($isRestitution);
                $user->save();

                //Reload the user list
                $this->init();
            }
            
            //If the action is "edit-8", so we have to edit the user with "pk = 8"
            if(substr($_POST["action"], 0, 4) == "edit"){
                $id = substr($_POST["action"], 5);
                $this->getSession()->saveUserEdit(User::get($id));
                $this->getSession()->saveParticipant(Participant::searchFromPk($this->getSession()->getUserEdit()->getParticpant_id()));
                header("Location: " . Constants::getPath(Constants::$PAGE_AJOUT_UTILISATEUR));
                die();
            }
            
            //If the action is "del-8", so we have to delete the user with "pk = 8"
            if(substr($_POST["action"], 0, 3) == "del"){
                //Extract the user's id
                $id = substr($_POST["action"], 4);
                
                //Remove the user
                $user = User::get($id);
                $user->remove();
                
                //Reload the user list
                $this->init();
            }
        }
    }
    
    
    /*----------- Render ARRAY -----------*/
    public function renderTab() {
//        foreach($this->basket->getAll() as $item){
        foreach($this->users as $item){
             echo $this->renderRow($item);
        }
    }
    
    /**
     * 
     * @param User $user
     * @return type String
     */
    private function renderRow($user){
        return '<tr>
                <td> <label name="login">' . $user->getLogin() . '</label> </td>
                <td> <input type="checkbox" name="isAdmin' . $user->getPk() . '" ' . $this->isChecked($user->getIsAdmin())  . '/> </td>
                <td> <input type="checkbox" name="isDepot' . $user->getPk() . '" ' . $this->isChecked($user->getIsDepot()) . '/> </td>
                <td> <input type="checkbox" name="isVente' . $user->getPk() . '" ' . $this->isChecked($user->getIsVente()) . '/> </td>
                <td> <input type="checkbox" name="isRestitution' . $user->getPk() . '" ' . $this->isChecked($user->getIsRestitution()) .' /></td>
                <td class="notPrintable"><button name="action" type="submit" value="update-' . $user->getPk() . '">Modifier</button></td>
                <td class="notPrintable"><button name="action" type="submit" value="edit-' . $user->getPk() . '">Détails</button></td>
                <td class="notPrintable"><button name="action" type="submit" value="del-' . $user->getPk() . '">Supprimer</button></td>
            </tr>';
    }
    
    private function isChecked($bool){
        if($bool > 0)
            return "checked";
    }
}
