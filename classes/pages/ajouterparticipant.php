<?php
namespace classes\pages;

use classes\db\object\Participant;
use classes\pages\Page;
use classes\config\Constants;
use classes\db\object\PC;

class AjouterParticipant extends Page {

    /**
     * @var \classes\db\object\Participant
     */
    private $vendeur;
    /**
     * @var bool
     */
    private $participantFind;
    /**
     * @var bool
     */
    private $showWarning;
    /**
     * @var PC
     */
    private $pc;

    public function __construct() {
        parent::__construct();
        $this->vendeur = new Participant();
        $this->participantFind = false;
        $this->showWarning = false;
    }

    /**
     * 
     * @return \classes\db\object\Participant
     */
    public function getVendeur() {
        return $this->vendeur;
    }

    private function init() {
        $this->vendeur = $this->getSession()->getParticipant();
        $this->pc = $this->getSession()->getPc();
    }
    
    /**
     * Search for a Participant
     */
    private function search() {
        $this->vendeur = Participant::search($_POST["nom"], $_POST["prenom"]);
        //If the search return nothing, juste create a new Participant with the first and last name to set their values in form
        if($this->vendeur->isEmpty()){
            $this->vendeur = new Participant("", strtoupper($_POST["nom"]), strtoupper($_POST["prenom"]));
        }
        $this->getSession()->saveParticipant($this->vendeur);
    }

    /**
     * Add a new Participant
     */
    private function add() {
        $adresse = !empty($_POST['adresse']) ? strtoupper($_POST["adresse"]) : "";
        $code_postal = !empty($_POST['code_postal']) ? $_POST["code_postal"] : "";
        $ville = !empty($_POST['ville']) ? strtoupper($_POST["ville"]) : "";
        $email = !empty($_POST['email']) ? strtoupper($_POST["email"]) : "";
        $tel = !empty($_POST["tel"]) ? strtoupper($_POST["tel"]) : "";

        if (isset($_GET['depot'])) {
            $this->vendeur->setType(1); // VENDEUR
        } else if (isset($_GET['vente'])) {
            $this->vendeur->setType(2); // ACHETEUR
        } else if (isset($_GET['restitution'])) {
            $this->vendeur->setType(1); // VENDEUR
        } else if (isset($_GET['utilisateur'])) {
            $this->vendeur->setType(3); // REDACTEUR
        }

        $this->vendeur = new Participant(
            $this->vendeur->getId(),
            $this->vendeur->getNom(),
            $this->vendeur->getPnom(),
            $adresse,
            $code_postal,
            $ville,
            $email,
            $tel,
            $this->vendeur->getType(),
            $this->vendeur->getRef()
        );
        
        if ($this->vendeur->checkBeforeInsert()) {
            //If it's a new Participant
            if($this->vendeur->getRef() === null || trim($this->vendeur->getRef()) === '' || $this->vendeur->getRef() == 0){
                //Counter
                $counter = $this->pc->getNextCounter();
                $this->pc->setCounter($counter);
                $this->pc->update();
                $this->getSession()->savePc($this->pc);
                $this->vendeur->setRef($counter);
            }
            $this->vendeur->save();
            $this->getSession()->saveParticipant($this->vendeur);
        }
    }

    public function pageProcess() {
        $this->init();

        if (isset($_POST["action"])) {

            //Search
            if ($_POST["action"] == "search") {
                if (!empty($_POST["ref"])) {
                    $this->vendeur = \classes\db\object\Participant::searchByRef($_POST["ref"]);
                    if($this->vendeur->isEmpty()){
                        $vendeur = new \classes\db\object\Participant();
                        $vendeur->setRef($_POST['ref']);
                        $this->vendeur = $vendeur;
                    }
                    $this->getSession()->saveParticipant($this->vendeur);
                } else if (!empty($_POST["nom"]) && !empty($_POST["prenom"])) {
                    $this->search();
                }
            }

            //Add
            if ($_POST["action"] == "add" && !empty($_POST["nom"]) && !empty($_POST["prenom"]) && !empty($_POST["tel"])
            ) {
                $this->add();
            }
            
            //Update
            if($_POST["action"] == "update"){
                $this->add();
            }
            
            //Continue to page "Depot"
            if($_POST["action"] == "depot"){
                //Save eventually modifications if user doesn't uses the other saving button
                $this->add();
                header("Location: ".\classes\config\Constants::getPath(\classes\config\Constants::$PAGE_DEPOT));
            }
            
            //Continue to page "Vente"
            if($_POST["action"] == "vente"){
                //Save eventually modifications if user doesn't uses the other saving button
                $this->add();
                header("Location: ". \classes\config\Constants::getPath(\classes\config\Constants::$PAGE_VENTE));
                die;
            }
            
            //Continue to page "Restitution"
            if($_POST["action"] == "restitution"){
                //Save eventually modifications if user doesn't uses the other saving button
                $this->add();
                header("Location: ". \classes\config\Constants::getPath(\classes\config\Constants::$PAGE_RESTITUTION));
                die;
            }
            
            //Continue to page "Utilisateur"
            if($_POST["action"] == "utilisateur"){
                header("Location: ". \classes\config\Constants::getPath(\classes\config\Constants::$PAGE_AJOUT_UTILISATEUR));
                die;
            }
        }
        
        if($this->vendeur->getId() != ""){
            $this->participantFind = true;
            $this->showWarning = false;
        } else if($this->vendeur->getNom () != "" || $this->vendeur->getPnom () != "" || !empty($this->vendeur->getRef())){
            $this->participantFind = false;
            $this->showWarning = true;
        } else{
            $this->participantFind = false;
            $this->showWarning = false;
        }
    }
    
    public function renderButton(){
        if(isset($_GET['depot'])){
            echo '<button name="action" type="submit" value="depot">Continuer vers Dépôt</button>';
        }
        else if(isset($_GET['vente'])){
            echo '<button name="action" type="submit" value="vente">Continuer vers Vente</button>';
        }
        else if(isset($_GET['restitution'])){
            echo '<button name="action" type="submit" value="restitution">Continuer vers Restitution</button>';
        }
        else if(isset($_GET['utilisateur'])){
            echo '<button name="action" type="submit" value="utilisateur">Ajouter un utilisateur</button>';
        }
    }
    
    function getParticipantFind() {
        return $this->participantFind;
    }
    
    function getAction(){
        if(isset($_POST['depot'])){
            return "?depot";
        }
        else if(isset($_POST['vente'])){
            return "?vente";
        }
        else if(isset($_POST['restitution'])){
            return "?restitution";
        }
        
        else if(isset($_POST['utilisateur'])){
            return "?utilisateur";
        }
    }
    
    function getErrorMessage() {
        return $this->showWarning ? "Cette personne n'existe pas" : null;
    }
}
