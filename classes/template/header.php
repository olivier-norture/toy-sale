<?php
namespace classes\template;

use classes\template\Template;
use classes\config\Constants;

/**
 * Description of header
 */
class header implements Template{
    
    private function isMainCurrent($page, $pageName, $pageAction=""){
        if($page->getPageName() == $pageName){
            return "main_current";
        }
        else if($pageName == Constants::$PAGE_DEPOT && $page->getPageName() == Constants::$PAGE_AJOUT_PARTICIPANT && isset ($_GET["depot"])){
            return "main_current";
        }
        else if($pageName == Constants::$PAGE_VENTE && $page->getPageName() == Constants::$PAGE_AJOUT_PARTICIPANT && isset($_GET["vente"])){
            return "main_current";
        }
        else if($pageName == Constants::$PAGE_RESTITUTION && $page->getPageName() == Constants::$PAGE_AJOUT_PARTICIPANT && isset($_GET["restitution"])){
            return "main_current";
        }
        return;
    }
    
    /**
     * @param \classes\pages\Page $page
     */
    public function render($page) {
        $html = '
        <div id ="header" class="notPrintable">
                <h1><a href="index.php">Bourse Aux Jouets</a></h1>';

        if($page->getUser() != null){
            if($page->getUser()->getPk() != null){
                            
        
                $html .='<div id="main_menu">
                    <ul>
                        <li class="first_list"><a href="index.php" class="main_menu_first '.$this->isMainCurrent($page, Constants::$PAGE_INDEX).'">Accueil</a></li>';
                
                $html .= '<li class="first_list"><a href="recherche.php" class="main_menu_first '.$this->isMainCurrent($page, Constants::$PAGE_RECHERCHE).'">Recherche</a></li>';
                
                if($page->getUser()->getIsDepot()){
                    $html .= '<li class="first_list"><a href="depot_jouet.php?clear_session&depot" class="main_menu_first '.$this->isMainCurrent($page, Constants::$PAGE_DEPOT).'">Depot</a></li>';
                }
                
                if($page->getUser()->getIsVente()){
                    $html .= '<li class="first_list"><a href="vendre_jouet.php?clear_session&vente" class="main_menu_first '.$this->isMainCurrent($page, Constants::$PAGE_VENTE).'">Vente</a></li>';
                }
                
                if($page->getUser()->getIsRestitution()){
                    $html .= '<li class="first_list"><a href="restitution_jouet.php?clear_session&restitution" class="main_menu_first '.$this->isMainCurrent($page, Constants::$PAGE_RESTITUTION).'">Restitution</a></li>';
                }
                        
                        if($page->isAdmin()){
                            $html .= '
                            <li class="first_list with_dropdown">
                               <a href="#" class="main_menu_first '. 
                                    $this->isMainCurrent($page, Constants::$PAGE_ADMIN_BILAN) .
                                    $this->isMainCurrent($page, Constants::$PAGE_ADMIN_FACTURES) .
                                    $this->isMainCurrent($page, Constants::$PAGE_ADMIN_LISTE_GLOBALE) .
                                    $this->isMainCurrent($page, Constants::$PAGE_PARAMETRAGE) .
                                    $this->isMainCurrent($page, Constants::$PAGE_AJOUT_UTILISATEUR)
                                    .'">Administration</a>
                               <ul>
                                   <li class="second_list second_list_border '. $this->isMainCurrent($page, Constants::$PAGE_PARAMETRAGE) . '"><a href="parametrage.php?clear_session&restitution" class="main_menu_second">Parametrage</a></li>
                                   <li class="second_list second_list_border '. $this->isMainCurrent($page, Constants::$PAGE_AJOUT_UTILISATEUR) . '"><a href="ajout_utilisateur.php?clear_session&utilisateur" class="main_menu_second">Ajouter un utilisateur</a></li>
                                   <li class="second_list second_list_border '. $this->isMainCurrent($page, Constants::$PAGE_ADMIN_BILAN) . '"><a href="bilan.php" class="main_menu_second">Bilan</a></li>
                                   <li class="second_list second_list_border '. $this->isMainCurrent($page, Constants::$PAGE_ADMIN_FACTURES) . '"><a href="rechercher_facture.php" class="main_menu_second">Rechercher une facture</a></li>
                                   <li class="second_list second_list_border '. $this->isMainCurrent($page, Constants::$PAGE_ADMIN_LISTE_GLOBALE) . '"><a href="liste_globale.php" class="main_menu_second">Liste globale</a></li>
                                   <li class="second_list second_list_border '. $this->isMainCurrent($page, Constants::$PAGE_ADMIN_GERER_PCS) . '"><a href="pc.php" class="main_menu_second">Gérer PCs</a></li>
                                   <li class="second_list second_list_border '. $this->isMainCurrent($page, Constants::$PAGE_ADMIN_SERVER_VARS) . '"><a href="server_vars.php" class="main_menu_second">Variables serveur</a></li>
                                   
                                   <li class="second_list second_list_border '. $this->isMainCurrent($page, Constants::$PAGE_ADMIN_RESET_DB) . '"><a href="reset_db.php" class="main_menu_second">Reset DB</a></li>
                                </ul>
                           </li>';
                        }
                        
                    $html .= '<li class="first_list"> <a href="deconnexion.php" class="main_menu_first">Déconnexion</a> </li>
                            </ul>';
                $html .= '</div> <!-- END #main_menu -->';
            }
        }
        $html .= '</div>';
        echo $html;
    }
}
