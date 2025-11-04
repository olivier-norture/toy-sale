<?php
namespace classes\pages;

use classes\config\ServerVarsConfig;
use classes\db\DB;
use classes\db\object\Participant;
use classes\db\object\PC;
use classes\SessionManager;
use classes\template\TemplateManager;
use classes\template\TemplateList;
use classes\config\Constants;
use classes\utils\Logger;

/**
 * Extends this class to create a new page.
 * Public methods are called from the web page.
 * You may have to create only one php object per web page.
 */
abstract class Page{

    /**
     * Datebase access
     * @var DB
     */
    protected $db;
    /**
     * Session access
     * @var SessionManager
     */
    private $session;
    /**
     * @var TemplateManager
     */
    protected $templateManager;
    /**
     * @var string The error message in the page
     */
    private $errorMessage;
    /**
     * @var Participant
     */
    private $redactor;
    /**
     * @var PC 
     */
    private $pc;

    function __construct(){
        $this-> db = new DB();
        $this->session = new SessionManager();
        $this->templateManager = new TemplateManager();
    }

    /**
     * Called before the page was fully rendered
     */
    abstract protected function pageProcess();
    
    private function pageManager(){
        //Need to save the redacteur's actions flow and reuse it in some case
        
        //Need to save the action, like "depot", "vente", "restitution" to
        //change the header in the "ajout_participant.php"
    }
    
    /**
     * Provide the globall process of a Page.
     */
    private function globalProcess($redirect = true){
        ServerVarsConfig::init();
        $this->initPc();
//        Logger::log("page", "DEBUG", $_SESSION[Constants::$SESSION_KEY], TRUE);
        
        //If the url contains "?clear_session" then remove the stored participant
        if (isset($_GET["clear_session"])) {
            $this->session->clearParticipant();
            $this->session->clearBasket();
            $this->session->clearBill();
            $this->session->clearUserEdit();
        }
        
        $pageName = explode("/", $_SERVER['PHP_SELF']);
        $pageName = $pageName[count($pageName)-1];
        
        //If there is no participant in session, then go to the participant's page
        if($this->session->getParticipant()->isEmpty() && $pageName != "ajout_participant.php" && $redirect){
            if(isset($_GET['depot']))
                $action = "?depot";
            else if(isset($_GET['vente']))
                $action = "?vente";
            else if(isset($_GET['restitution']))
                $action = '?restitution';
            else if(isset($_GET['utilisateur']))
                $action = '?utilisateur';
            header("Location:  ". Constants::getPath(Constants::$PAGE_AJOUT_PARTICIPANT) . $action);
            die();
        }
    }
    
    /**
     * The main method of a Page.
     */
    public function process($redirect = true){
        $this->globalProcess($redirect);
        $this->pageProcess();
    }
      
    /**
     * Get the Session Manager
     * @return SessionManager The Session Manager
     */
    protected  function getSession(){
        return $this->session;
    }
    
    /**
     * Return the current date in format : DD/MM/YYYY
     * @return string The current date
     */
    public function getCurrentDate(){
        \classes\utils\Date::getCurrentDate();
    }
    
    /**
     * Return the current redacteur
     * @return Participant
     */
    public function getRedacteur(){
        return $this->session->getRedacteur();
    }
    
    protected function setRedacteur($redactor){
        return $this->redactor = $redactor;
    }
    
    /**
     * Render the template which contains all participant's informations.
     */
    public function renderTemplateParticipantInfo(){
        TemplateManager::renderTemplate(TemplateList::$PARTICIPANT_INFO, $this);
    }
    
    /**
     * Render the template which contains the page header
     */
    public function renderTemplateHeader(){
        TemplateManager::renderTemplate(TemplateList::$HEADER, $this);
    }
    
    /**
     * Render the template which contains the page header for printing
     */
    public function renderTemplateHeaderPrint(){
        TemplateManager::renderTemplate(TemplateList::$HEADER_PRINT, $this);
    }
    
    /**
     * Render the template which contains the page header
     */
    public function renderTemplateFooter(){
        TemplateManager::renderTemplate(TemplateList::$FOOTER, $this);
    }
    
    /**
     * Render the template which contains the page header
     */
    public function renderTemplateFooterPrint(){
        TemplateManager::renderTemplate(TemplateList::$FOOTER_PRINT, $this);
    }

    /**
     * Get the web page's name
     * @return string The page's name
     */
    public function getPageName(){
        $pageName = explode("/", $_SERVER['PHP_SELF']);
        $pageName = $pageName[count($pageName)-1];
        return $pageName;
    }
    
    public function getErrorMessage(){
        return $this->errorMessage;
    }
    
    protected function setErrorMessage($msg){
        $this->errorMessage .= "\n" . $msg;
    }
    
    /**
     * Use this function to print with JavaScript
     * @return boolean true to start the print, else false
     */
    public function startPrint(){
        $tmp = isset($_GET["print"]) ? 1 : 0;
        Logger::log("vendre_jouet", "DEBUG", "startPrint returns : " . $tmp);
        return $tmp;
    }
    
    /**
     * 
     * @return boolean
     */
    public function isAdmin(){
//        return true;
        return $this->session->getUser()->getIsAdmin();
    }
    
    /**
     * @return User
     */
    public function getUser(){
        return $this->session->getUser();
    }
    
    private function initPc(){
        $this->pc = $this->session->getPc();
        
        if($this->pc == null){
            $this->pc = PC::search(array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);
            $this->session->savePc($this->pc);
        }
    }
    
    public function getLetter(){
        return $this->pc->getLetter() != null ? $this->pc->getLetter() : Constants::$DEFAULT_LETTER;
    }
    
    public function disconnect(){
        \classes\utils\Session::clearAll();
    }
}
