<?php
namespace classes\db\object;

use classes\config\Constants;

class objetUtils {
    static function extractVendorPkFromRef($ref){
        return substr(explode("-", $ref)[0], 1);
    }
    
    static function extractObjectPkFromRef($ref){
        return explode("-", $ref)[1];
    }
    
    static function getObjectPkFromRef($ref){
        $stmt = \classes\db\DB::getConn()->prepare("select PK from objet where vendeur_PK = :vendeur_PK and id = :id");
        $stmt->bindValue(":vendeur_PK", objetUtils::extractVendorPkFromRef($ref));
        $stmt->bindValue(":id", objetUtils::extractObjectPkFromRef($ref));
        $stmt->execute();
        return $stmt->fetch()[0];
    }
    
    /**
     * Validate the form of the given reference.
     * Return true if the reference like Axxx-yyy otherwise false
     * @param string $ref
     */
    static function validateRef($ref){
        return preg_match("/^[A-Za-z][0-9]*-[0-9]*+/", $ref, $matches, PREG_OFFSET_CAPTURE) > 0;
    }
    
    /**
     * Format the given ref, like "A1-1" to give it the following form : "A001-001"
     * @param string $ref The ref to format
     * @return string the nex formatted ref
     */
    static function formatRef($ref){
        $arr = explode(Constants::$REF_SEPARATOR, $ref);
        
        $letter = substr($arr[0], 0, 1);
        $vendorRef = substr($arr[0], 1);
        $objectRef = substr($arr[1], 0);
        
        return $letter .
               str_pad($vendorRef, Constants::$REF_OBJECT_SIZE, Constants::$REF_CHAR_COMPLETE, STR_PAD_LEFT) .
               Constants::$REF_SEPARATOR .
               str_pad($objectRef, Constants::$REF_OBJECT_SIZE, Constants::$REF_CHAR_COMPLETE, STR_PAD_LEFT);
    }
    
    /**
     * Return the next object reference whitch will be used.
     * For print only. Don't use this method to force an object's id
     * @param Participant $vendor The Participant from wich retrieves the PK
     * @param PC $pc
     * @return string The next object's REF
     */
    public static function getNextRef($vendor, $letter){
        $obj = new Objet("", "", $vendor->getId());
        $obj->setLetter($letter);
        $obj->setId($obj->getNextId());
        return objetUtils::getObjectRef($obj, $vendor);
    }

    /**
     * 
     * @param Objet $object
     * @param Participant $vendor
     * @param string $overwriteLetter
     * @return string
     */    
    public static function getObjectRef($object, $vendor, $overwriteLetter = null){
        $letter = $overwriteLetter != null ? $overwriteLetter : $object->getLetter();
        return $letter . str_pad($vendor->getRef(), Constants::$REF_OBJECT_SIZE, Constants::$REF_CHAR_COMPLETE, STR_PAD_LEFT)
                . "-" . str_pad($object->getId(), Constants::$REF_OBJECT_SIZE, Constants::$REF_CHAR_COMPLETE, STR_PAD_LEFT);
    }
}
