<?php
namespace classes\pages;

use classes\db\object\Participant;
use classes\pages\Page;
use classes\config\Constants;
use classes\db\object\PC;

class PagePC extends Page {

    public function __construct() {
        parent::__construct();
        $this->showWarning = false;
    }

    private function init() {
        $this->pc = $this->getSession()->getPc();
    }    

    private function createPc() {        
        $letter = !empty($_POST['letter']) ? strtoupper($_POST["letter"]) : "";
        $ip = !empty($_POST['ip']) ? $_POST["ip"] : "";
        $counter = !empty($_POST['counter']) ? strtoupper($_POST["counter"]) : "";
        
        $pc = new PC(null, $ip, $letter, $counter);
        $pc->save();
    }
    
    private function update() {
        
    }
    
    public function tableRows(){
        $html = "";
        foreach(\classes\db\object\PC::find() as $pc){
            $html .= $this->renderRow($pc);
        }
        echo $html;
    }
    
    /**
     * 
     * @param \classes\db\object\PC pc
     */
    private function renderRow($pc){
        $html = '<tr>
                 <form action="#" method="post" id="updatePc">
                         <input type="text" name="id" style="display: none" value="' . $pc->id . '"/>
                    <td> <input type="text" name="letter" value="' . $pc->letter . '"/> </td>
                .   <td> <input type="text" name="ip" value="' . $pc->ip . '"/> </td>
                    <td> <input type="text" name="counter" value ="' . $pc->counter . '"/> </td>
                    <td><button name="action" type="submit" value="update">Mettre a jour</button></td>
                    <td><button name="action" type="submit" value="delete">Supprimer</button></td>
                  </form>
                . </tr>';
        return $html;
    }

    public function pageProcess() {


        if (isset($_POST["action"])) {
            
            $id = !empty($_POST['id']) ? $_POST["id"] : null;
            $letter = !empty($_POST['letter']) ? strtoupper($_POST["letter"]) : "";
        $ip = !empty($_POST['ip']) ? $_POST["ip"] : "";
        $counter = !empty($_POST['counter']) ? strtoupper($_POST["counter"]) : "";
        $pc = new PC($id, $ip, $letter, $counter);
            
            switch($_POST["action"])
{
 case 'create':
  $pc->save();
  break;

 case 'update':
  $pc->save();
  break;

 case 'delete':
  $pc->delete();
  break;

 default :
  break;
}
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
