<?php
namespace classes\utils;

use classes\config\Constants;

class Session {
    /**
     * Retrieve the value of an object from his key
     * @param string $key The object's key to retrieve
     * @return Object The value stored in the session
     */
    public static function get($key){
        if(Session::contains($key)){
            Logger::log("session", "DEBUG", "GET : key '". $key ."' found");
            return $_SESSION[$key];
        }
        Logger::log("session", "DEBUG", "GET : key '". $key ."' not found");
        return null;
    }
    
    /**
     * Store an object in the sessuin
     * @param string $key The object's key
     * @param Object $value The boject's value
     */
    public static function set($key, $value){
        $_SESSION[$key] = $value;
        //Add the key to the key's list
        if (!Session::contains($key)) {
            $_SESSION[Constants::$SESSION_KEY][] = $key;
        }
        Logger::log("session", "TRACE", "key '". $key . "' set in the session");
    }
    
    /**
     * Remove a key
     * @param string $key
     */
    public static function remove($key){
        Session::set($key, null);
        //Remove the key from the keys's list
        $_SESSION[Constants::$SESSION_KEY] = Session::recreateWithout($_SESSION[Constants::$SESSION_KEY]);
    }
    
    /**
     * Clear all objects stored in the session
     */
    public static function clearAll(){
        foreach($_SESSION[Constants::$SESSION_KEY] as $key){
            Session::set($key, null);
        }
        //Clear the keys's list
        $_SESSION[Constants::$SESSION_KEY] = null;
    }

    /**
     * Recreate the keys's list without the given key
     * @param string $key The key to removes
     * @return string[] The new keys's list
     */    
    private static function recreateWithout($key){
        $keys = array();
        foreach($_SESSION[Constants::$SESSION_KEY] as $k){
            if($k != $key){
                $keys[] = $k;
            }
        }
        return $keys;
    }
    
    /**
     * Check if the session contains the given key
     * @param string $key The key to find
     * @return boolean True if the key exist, otherwithe false
     */
    private static function contains($key){
        if (!isset($_SESSION[Constants::$SESSION_KEY]) || !is_array($_SESSION[Constants::$SESSION_KEY])) {
            return false;
        }
        return in_array($key, $_SESSION[Constants::$SESSION_KEY]);
    }
}
