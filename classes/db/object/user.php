<?php

namespace classes\db\object;

use PDO;
use classes\db\DB;

class User {

    private $id;
    private $login;
    private $password;
    private $isAdmin;
    private $isDepot;
    private $isVente;
    private $isRestitution;
    private $participant_id;

    /**
     * 
     * @param type $id
     * @param type $login
     * @param type $password
     * @param type $idAdmin
     * @param type $isDepot
     * @param type $isVente
     * @param type $isRestitution
     * @param type $participant_id
     */
    public function __construct($id = null, $login = "", $password = "", $idAdmin = false, $isDepot = false, $isVente = false, $isRestitution = false, $participant_id = null){
        $this->id = $id;
        $this->login = $login;
        $this->password = $password;
        $this->isAdmin = $idAdmin;
        $this->isDepot = $isDepot;
        $this->isVente = $isVente;
        $this->isRestitution = $isRestitution;
        $this->participant_id = $participant_id;
    }

    /*-------------- SETTER --------------*/
    
    public function setPk($id){
        $this->id = $id;
    }

    public function setLogin($login){
        $this->login = $login;
    }
    
    public function setPassword($password){
        $this->password = $password;
    }

    public function setIsAdmin($isAdmin){
        $this->isAdmin = $isAdmin;
    }

    public function setIsDepot($isDepot){
        $this->isDepot = $isDepot;
    }

     public function setIsVente($isVente){
        $this->isVente = $isVente;
    }
    
    public function setIsRestitution($isRestitution){
        $this->isRestitution = $isRestitution;
    }

    public function setParticpant_id($particpant_id){
        $this->particpant_id = $particpant_id;
    }
    
    /*-------------- GETTER --------------*/

   public function getPk(){
        return $this->id;
    }

    public function getLogin(){
        return $this->login;
    }
    
    public function getPassword(){
        return $this->password;
    }

    public function getIsAdmin(){
        return $this->isAdmin;
    }

    public function getIsDepot(){
        return $this->isDepot;
    }

     public function getIsVente(){
        return $this->isVente;
    }
    
    public function getIsRestitution(){
        return $this->isRestitution;
    }

    public function getParticpant_id(){
        return $this->participant_id;
    }

    /*------------ BUSINESS METHODS ----------------*/
    
    public function getParticipant($participant_id)
    {
        $stmt = DB::getConn()->prepare("select pk, nom, prenom, adresse, cp, ville, email, tel, type from participant where PK = :participant_id");
        $stmt->bindValue(':participant', $participant_id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT);
        return new Participant($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], $row[8]);
    }
    
    /*
     * Get a specific user
     */
    public function getUserFromParticipant($participant_id)
    {
        $stmt = DB::getConn()->prepare("
SELECT u.pk, u.login, u.password, u.isAdmin, u.isDepot, u.isVente, u.isRestitution, u.participant_pk 
FROM User u
WHERE u.participant_PK = :participant_id");
        $stmt->bindValue(':participant', $participant_id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT);
        return new User($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7]);
    }
    
    /*
     * get user from participant name and firstname
     */
    public function getUser($nom,$prenom)
    {
         $stmt = DB::getConn()->prepare("select pk from participant where nom = :nom and prenom = :prenom");
        $stmt->bindValue(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindValue(':prenom', $prenom, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT);
        return getUserFromParticipant($row[0]);
    }
    
    /**
     * 
     * @param type $login
     * @param type $password
     * @return User
     */
    public static function searchForConnect($login, $password){
        $stmt = DB::getConn()->prepare("select pk from user where login = :login and password = :password");
        $stmt->bindValue(":login", $login);
        $stmt->bindValue(":password", $password);
        $stmt->execute();
        
        return User::get($stmt->fetch()["pk"]);
    }
    
    /**
     * Retrieve a user from it's ID
     * @param int $id the user's ID
     * @return User
     */
    public static function get($id){
        $stmt = DB::getConn()->prepare("
SELECT u.pk, u.login, u.password, u.isAdmin, u.isDepot, u.isVente, u.isRestitution, u.participant_PK 
FROM user u
WHERE u.pk = :pk");
        $stmt->bindValue(':pk', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT);
        return new User($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7]);
    }
    
    /*
     * Get the list of all users
     */
    public static function getAll($orderBy = "asc"){
        $orderBy = $orderBy == "asc" ? "asc" : "desc";
        $users = array();
        
        $stmt = DB::getConn()->prepare("SELECT pk, login, password, isAdmin, isDepot, isVente, isRestitution, participant_PK FROM user ORDER BY login ". $orderBy);
        $stmt->execute();

        if($stmt->rowCount() > 0 )
        {
            while($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT))
            {
                array_push($users, new User($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7]));
            }
        }
        return $users;
    }
    
    public function checkBeforeInsert(){
        return !empty($this->login) && !empty($this->password);
    }

    public function save(){
        if($this->id == null){
            $stmt = DB::getConn()->prepare("
insert into user(login,password,isAdmin,isDepot,isVente,isRestitution,participant_pk)
values (:login,:password,:isAdmin,:isDepot,:isVente,:isRestitution,:participant_pk)");
            $stmt->bindValue(":login", $this->login, PDO::PARAM_STR);
            $stmt->bindValue(":password", $this->password, PDO::PARAM_STR);
            $stmt->bindValue(":isAdmin", $this->isAdmin, PDO::PARAM_BOOL);
            $stmt->bindValue(":isDepot", $this->isDepot, PDO::PARAM_BOOL);
            $stmt->bindValue(":isVente", $this->isVente, PDO::PARAM_BOOL);
            $stmt->bindValue(":isRestitution", $this->isRestitution="TRUE"?1:0, PDO::PARAM_BOOL);
            $stmt->bindValue(":participant_pk", $this->participant_id, PDO::PARAM_INT);

            DB::getConn()->beginTransaction();
            $stmt->execute();
            //extract the id and set it to the current object
            $this->id = DB::getConn()->lastInsertId();
            DB::getConn()->commit();
        }
        else{
            $stmt = DB::getConn()->prepare("
UPDATE user
SET login = :login, 
    password = :password, 
    isAdmin = :isAdmin, 
    isDepot = :isDepot, 
    isVente = :isVente, 
    isRestitution = :isRestitution, 
    participant_pk = :participant_pk
WHERE pk = :pk");
            $stmt->bindValue(":login", $this->login, PDO::PARAM_STR);
            $stmt->bindValue(":password", $this->password, PDO::PARAM_STR);
            $stmt->bindValue(":isAdmin", $this->isAdmin, PDO::PARAM_BOOL);
            $stmt->bindValue(":isDepot", $this->isDepot, PDO::PARAM_BOOL);
            $stmt->bindValue(":isVente", $this->isVente, PDO::PARAM_BOOL);
            $stmt->bindValue(":isRestitution", $this->isRestitution, PDO::PARAM_BOOL);
            $stmt->bindValue(":participant_pk", $this->participant_id, PDO::PARAM_INT);
            $stmt->bindValue(":pk", $this->id, PDO::PARAM_INT);

            DB::getConn()->beginTransaction();
            $stmt->execute();
            DB::getConn()->commit();
        }
    }
    
    public function isEmpty(){
        return empty($this->nom) && empty($this->pNom);
    }
    
    public function remove(){
        $stmt = DB::getConn()->prepare("delete from user where pk = :pk");
        $stmt->bindValue(":pk", $this->id);
        $stmt->execute();
    }

}

?>
