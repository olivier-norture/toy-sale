<?php
namespace classes\config;

class Constants{
    public static function APP_BASE_PATH() {
        // return "/bourseauxjoeuts";
        return "/";
    }
    public static $SERVER_PATH = "/var/www/html/bourseauxjoeuts";

    private $DB_HOST;
    private $DB_USERNAME;
    private $DB_PASSWORD;
    private $DB_NAME;

    public function __construct() {
        $this->DB_HOST = getenv('DB_HOST') ?: "localhost";
        $this->DB_USERNAME = getenv('DB_USER') ?: "root";
        $this->DB_PASSWORD = getenv('DB_PASSWORD') ?: "";
        $this->DB_NAME = getenv('DB_DATABASE') ?: "bourseauxjouets";
    }

    public function getDbHost() {
        return $this->DB_HOST;
    }

    public function getDbUsername() {
        return $this->DB_USERNAME;
    }

    public function getDbPassword() {
        return $this->DB_PASSWORD;
    }

    public function getDbName() {
        return $this->DB_NAME;
    }
    
    public static $PAGE_INDEX = "index.php";
    public static $PAGE_RECHERCHE = "recherche.php";
    public static $PAGE_DEPOT = "depot_jouet.php";
    public static $PAGE_VENTE = "vendre_jouet.php";
    public static $PAGE_RESTITUTION = "restitution_jouet.php";
    public static $PAGE_AJOUT_PARTICIPANT = "ajout_participant.php";
    public static $PAGE_AJOUT_UTILISATEUR = "ajout_utilisateur.php";
    public static $PAGE_PARAMETRAGE = 'parametrage.php';
    public static $PAGE_ADMIN_BILAN = 'bilan.php';
    public static $PAGE_ADMIN_FACTURES = 'rechercher_facture.php';
    public static $PAGE_ADMIN_LISTE_GLOBALE = 'liste_globale.php';
    public static $PAGE_ADMIN_GERER_PCS = 'pc.php';
    public static $PAGE_ADMIN_SERVER_VARS = 'server_vars.php';
    public static $PAGE_ADMIN_UPDATE = 'update.php';
    public static $PAGE_ADMIN_RESET_DB = 'reset_db.php';
    
    public static $PATH_ROOT = "/var/www/html/bourseauxjoeuts";
    
    public static $PATH_IMG = "/var/www/html/bourseauxjoeuts/web/images";
    public static $PATH_IMG_LOGO = "/var/www/html/bourseauxjoeuts/web/images/logo.png";
    
    public static $PATH_WEB = "web";
    
    public static $URL_ACTION_CLEAR_SESSION = "clear_session";
    public static $URL_HEADER_DEPOT = "ajout_participant.php?clear_session&depot";
    public static $URL_HEADER_VENTE = "ajout_participant.php?clear_session&vente";
    public static $URL_HEADER_RESTITUTION = "ajout_participant.php?clear_session&restitution";
    
    public static $LOG_FILE = "/tmp/bourseauxjouets.log";
    
    public static $REF_OBJECT_SIZE = 3;
    public static $REF_BILL_SIZE = 4;
    public static $REF_CHAR_COMPLETE = 0;
    public static $REF_SEPARATOR = "-";
    
    public static $MESSAGE_VENTE_SOME_OBJECTS_ALREADY_SELLED = "Un objet des objets a déjà été vendu";
    public static $MESSAGE_VENTE_VENDOR_PK = "Aucun vendeur renseigné";
    public static $MESSAGE_VENTE_NOT_ENOUGH_MONEY = "Le montant payé est inférieur au total de la facture";
    public static $MESSAGE_VENTE_BILL_INACTIVE = "Cette facture est désactivé";
    public static $MESSAGE_OLD_BILL = "Cette facture a été annulée et remplacée par la facture";
    public static $MESSAGE_VENTE_OBJECT_ALREADY_SELLED = "Cet objet a déjà été vendu !";
    public static $MESSAGE_OBJECT_DOESNT_EXIST = "Cet objet n'existe pas !";
    
    public static $SESSION_KEY = "SESSION_KEY";
    
    public static $BILL_TYPE_DEPOT = "DEPOT";
    public static $BILL_TYPE_VENTE = "VENTE";
    public static $BILL_TYPE_RESTITUTION = "RESITUTION";
    
    public static $DEFAULT_LETTER = "Z";
    
    public static $RESTITUTION_BILAN_CSV_PATH = "bilan_restitution.csv";

    public static function getPath($webPage){
        $path = Constants::APP_BASE_PATH();
        if ($path === '/') {
            $path = '';
        }
        return $path . "/" . Constants::$PATH_WEB . "/" . $webPage;
    }
}

?>
