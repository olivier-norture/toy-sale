<?php
namespace classes\template;

use classes\config\ServerVarsConfig;
use classes\template\Template;

class ParticipantInfo implements Template{
    public static $date = null;
    
    public function render($page){
        if (ParticipantInfo::$date == null) {
            ParticipantInfo::$date = \classes\utils\Date::getCurrentDateAndTime();
        }

        echo '
<div style="display: block">
    <div style="display: block">
        <label class="labelInfoTitle labelInfo">Date de la bourse au jouet : </label><label class="labelInfo">' . ServerVarsConfig::getServerVar(ServerVarsConfig::$DATE_DEBUT) . ' - ' . ServerVarsConfig::getServerVar(ServerVarsConfig::$DATE_FIN) . '</label>
        <label class="labelInfoTitle labelInfo">Date du jour : </label><label class="labelInfo">' . ParticipantInfo::$date . '</label>
    </div>                        
    <div style="display: block">
        <label class="labelInfoTitle labelInfo">Nom : </label><label class="labelInfo">' . $page->getAcheteur()->getNom() . '</label>
        <label class="labelInfoTitle labelInfo">Prénom : </label><label class="labelInfo">' . $page->getAcheteur()->getPnom() . '</label>
    </div>
    <div style="display: block">
        <label class="labelInfoTitle labelInfo">Rédacteur : </label><label class="labelInfo">' . $page->getRedacteur()->getPnom() . '</label>
    </div>
</div>
        ';
    }
}
