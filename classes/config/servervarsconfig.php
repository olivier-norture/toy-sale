<?php
namespace classes\config;

use classes\db\object\ServerVar;

class ServerVarsConfig
{
    public static $SERVER_IP = "SERVER_IP";
    public static $DATE_DEBUT = "DATE_DEBUT";
    public static $DATE_FIN = "DATE_FIN";

    public static function factory()
    {
        return new ServerVarsConfig();
    }

    public static function getDefaultValues()
    {
        $defaultValues = [];
        $defaultValues[ServerVarsConfig::$SERVER_IP] = "127.0.0.1";
        $defaultValues[ServerVarsConfig::$DATE_DEBUT] = "01/01/1970";
        $defaultValues[ServerVarsConfig::$DATE_FIN] = "03/01/1970";

        return $defaultValues;
    }

    public static function initFromDB()
    {
        foreach (ServerVar::find() as $serverVar) {
            ServerVarsConfig::updateServerVar($serverVar->key, $serverVar->value);
        }
    }

    public static function init()
    {
        $defaultValues = ServerVarsConfig::getDefaultValues();
        foreach (ServerVar::find() as $serverVar) {
            $defaultValues[$serverVar->key] = $serverVar->value;
        }
        foreach ($defaultValues as $key => $value) {
            ServerVarsConfig::updateServerVar($key, $value);
        }
    }


    public static function updateServerVar($key, $value)
    {
        $GLOBALS[$key] = $value;
    }

    public static function getServerVar($key)
    {
        return $GLOBALS[$key];
    }
}