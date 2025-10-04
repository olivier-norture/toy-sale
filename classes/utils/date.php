<?php
namespace classes\utils;

class Date {
    public static function getCurrentDate(){
        date_default_timezone_set('Europe/Paris');
        return date("d/m/Y");
    }
    
    public static function getCurrentDateAndTime(){
        date_default_timezone_set('Europe/Paris');
        return date("d/m/Y H:i:s");
    }
}
