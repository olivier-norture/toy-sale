<?php
namespace classes\pages;

use classes\db\object\User;

class Index extends Page {
    
    protected function pageProcess() {
        // \classes\utils\Session::clearAll();
        
        if(isset($_POST["connect"])){
            $login = $_POST["login"];
            $password = $_POST["password"];
            
            $user = User::searchForConnect($login, $password);
            if($user->getPk() != null){
                //Save the user in session
                $this->getSession()->saveUser($user);
                //Load the correspondant Redactor
                $this->getSession()->saveRedacteur(\classes\db\object\Participant::searchFromPk($user->getParticpant_id()));
            }
            else{
                $this->setErrorMessage("Utilisateur inconnu");
            }
            
        }
    }

}