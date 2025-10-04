<?php
namespace classes\pages;

use classes\db\object\User;
use classes\db\object\Participant;
use classes\pages\Page;

class AjouterUtilisateur extends Page {
    
    /**
     * @var \classes\db\object\User 
     */
    private $editUser;
    /**
     * @var User
     */
    private $user;
    /**
     * @var \classes\db\object\Participant 
     */
    private $participant;

    public function __construct() {
        parent::__construct();
        $this->editUser = new User();
        $this->user = new User();
        $this->participant = new Participant();
    }

    /**
     * 
     * @return \classes\db\object\User
     */
    public function getUser() {
        return $this->user;
    }
    
    /**
     * 
     * @return User
     */
    public function getEditUser() {
        return $this->editUser;
    }
    
    /**
     * @return \classes\db\object\Participant
     */
    public function getParticipant(){
        return $this->participant;
    }

    private function init() {
        $this->user = $this->getSession()->getUser();
        $this->editUser = $this->getSession()->getUserEdit();
        $this->participant = $this->getSession()->getParticipant();
    }

    public function pageProcess() {
        $this->init();
        
        if (isset($_POST["action"])) {
            //Add
            if ($_POST["action"] == "add" && !empty($_POST["login"]) && !empty($_POST["password"])) {
                $this->add();
            }
            
            //Update
            if($_POST["action"] == "update"){
                $this->add();
            }
        }
    }
    
    /**
     * Add a new User
     */
    private function add() {
        $login = !empty($_POST['login']) ? $_POST["login"] : $this->editUser->getLogin();
        $password = !empty($_POST['password']) ? $_POST["password"] : $this->editUser->getPassword();
        $isAdmin = isset($_POST["isAdmin"]) ? 1 : 0;
        $isDepot = isset($_POST["isDepot"]) ? 1 : 0;
        $isVente = isset($_POST["isVente"]) ? 1 : 0;
        $isRestitution = isset($_POST["isRestitution"]) ? 1 : 0;
        
        $this->editUser = new User($this->editUser->getPk(), $login, $password, $isAdmin, $isDepot, $isVente, $isRestitution, $this->participant->getId());

        if ($this->editUser->checkBeforeInsert()) {
            $this->editUser->save();
            $this->getSession()->saveUserEdit($this->editUser);
        }
    }
}
