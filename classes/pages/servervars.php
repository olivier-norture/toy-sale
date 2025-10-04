<?php
namespace classes\pages;

use \classes\pages\Page;
use \classes\db\object\ServerVar;
use \classes\config\ServerVarsConfig;

class ServerVars extends Page {

    public function __construct() {
        parent::__construct();
        $this->showWarning = false;
    }
    
    public function tableRows(){
        $html = "";
        foreach(ServerVar::find() as $pc){
            $html .= $this->renderRow($pc);
        }
        echo $html;
    }
    
    /**
     * 
     * @param \classes\db\object\ServerVar serverVar
     */
    private function renderRow($serverVar){
        $html = '<tr>
                 <form action="#" method="post" id="updateServerVar">
                         <input type="text" name="id" style="display: none" value="' . $serverVar->id . '"/>
                    <td> <input type="text" name="key" value="' . $serverVar->key . '"/> </td>
                    <td> <input type="text" name="value" value="' . $serverVar->value . '"/> </td>
                    <td><button name="action" type="submit" value="update">Mettre a jour</button></td>
                    <td><button name="action" type="submit" value="delete">Supprimer</button></td>
                  </form>
                  </tr>';
        return $html;
    }

    public function pageProcess() {
        if (isset($_POST["action"])) {
            
            $id = !empty($_POST['id']) ? $_POST["id"] : null;
            $key = !empty($_POST['key']) ? strtoupper($_POST["key"]) : "";
            $value = !empty($_POST['value']) ? $_POST["value"] : "";
            $pc = new ServerVar($id, $key, $value);
            
            switch($_POST["action"]){
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
        ServerVarsConfig::init();
    }
    
}
