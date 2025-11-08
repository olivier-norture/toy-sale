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

    public static function format_to_sql($date_string) {
        if (empty($date_string)) {
            return null;
        }

        // Try to parse with time
        $date = \DateTime::createFromFormat('d/m/Y H:i:s', $date_string);
        if ($date !== false) {
            return $date->format('Y-m-d H:i:s');
        }

        $date = \DateTime::createFromFormat('Y-m-d H:i:s', $date_string);
        if ($date !== false) {
            return $date->format('Y-m-d H:i:s');
        }

        // As a fallback, try to parse without time
        $date = \DateTime::createFromFormat('d/m/Y', $date_string);
        if ($date !== false) {
            return $date->format('Y-m-d H:i:s');
        }
        
        $date = \DateTime::createFromFormat('Y-m-d', $date_string);
        if ($date !== false) {
            return $date->format('Y-m-d H:i:s');
        }

        return $date_string; // Return original string if no format matches
    }
}