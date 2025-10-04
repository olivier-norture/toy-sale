<?php
namespace classes\utils;

use classes\config\Constants;

class Logger {
    private $time;
    private $level;
    private $class;
    private $message;
    private $session;
    private $GET;
    private $POST;

    function __construct() {
    }
    
    static function log($class, $level, $message){
        error_reporting(E_ALL); ini_set('display_errors', 'on');
//        $this->class = $class;
//        $this->level = $level;
//        $this->message = $message;
        
//        echo "$class $level $message";
//        echo"\n";
        file_put_contents(Constants::$LOG_FILE, "$class ; $level ; $message\n", FILE_APPEND | LOCK_EX);
    }
        
}
