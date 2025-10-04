<?php
namespace classes\db\object;

use PDO;
use \classes\db\DB;

class ServerVar{
    public $id;
    public $key;
    public $value;
    
    public function __construct($id, $key, $value) {
        $this->id = $id;
        $this->key = $key;
        $this->value = $value;
    }
    
    public function delete(){
      $stmt = DB::getConn()->prepare("DELETE FROM server_vars WHERE id = :id");
      $stmt->bindValue(":id", $this->id, PDO::PARAM_STR);
      $stmt->execute();
    }
    
    public function save(){
        if($this->id == null){
            $stmt = DB::getConn()->prepare("
insert into server_vars(`key`, value)
values (UPPER(:key), UPPER(:value))");
            $stmt->bindValue(":key", $this->key, PDO::PARAM_STR);
            $stmt->bindValue(":value", $this->value, PDO::PARAM_STR);
            
            DB::getConn()->beginTransaction();
            $stmt->execute();
            //extract the id and set it to the current object
            $this->id = DB::getConn()->lastInsertId();
            DB::getConn()->commit();
        }
        else{
            $stmt = DB::getConn()->prepare("
update  server_vars
set     `key` = UPPER(:key),
        value = UPPER(:value)
where   id = :id
                    ");
            $stmt->bindValue(":key", $this->key, PDO::PARAM_STR);
            $stmt->bindValue(":value", $this->value, PDO::PARAM_STR);
            $stmt->bindValue(":id", $this->id, PDO::PARAM_STR);
            
            DB::getConn()->beginTransaction();
            $stmt->execute();
            DB::getConn()->commit();
        }
    }
    
    public static function find(){
        $stmt = DB::getConn()->prepare('select id, `key`, value from server_vars');
        $stmt->execute();

        $list = array();
        if($stmt->rowCount() > 0){
            foreach($stmt->fetchAll() as $row){
                $list[] = new ServerVar($row['id'], $row['key'], $row['value']);
            }
        }
        return $list;
    }
}