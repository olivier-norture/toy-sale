<?php
namespace classes\db\object;

use PDO;
use \classes\db\DB;

class PC{
    public $id;
    public $ip;
    public $letter;
    public $counter;
    
    public function __construct($id, $ip, $letter, $counter) {
        $this->id = $id;
        $this->ip = $ip;
        $this->letter = $letter;
        $this->counter = $counter;
    }

    public function getLetter(){
        return $this->letter;
    }
    
    public function setCounter($counter){
        $this->counter = $counter;
    }
    
    public function delete(){
      $stmt = DB::getConn()->prepare("DELETE FROM pc WHERE id = :id");
      $stmt->bindValue(":id", $this->id, PDO::PARAM_STR);
      $stmt->execute();
    }
    
    
    public function save(){
        if($this->id == null){
            $stmt = DB::getConn()->prepare("
insert into pc(letter, ip, counter)
values (UPPER(:letter), UPPER(:ip), :counter)");
            $stmt->bindValue(":letter", $this->letter, PDO::PARAM_STR);
            $stmt->bindValue(":ip", $this->ip, PDO::PARAM_STR);
            $stmt->bindValue(":counter", $this->counter, PDO::PARAM_INT);

            DB::getConn()->beginTransaction();
            $stmt->execute();
            //extract the id and set it to the current object
            $this->id = DB::getConn()->lastInsertId();
            DB::getConn()->commit();
        }
        else{
            $stmt = DB::getConn()->prepare("
update  pc
set     letter = UPPER(:letter),
        ip = UPPER(:ip),
        counter = :counter
where   id = :id
                    ");
            $stmt->bindValue(":letter", $this->letter, PDO::PARAM_STR);
            $stmt->bindValue(":ip", $this->ip, PDO::PARAM_STR);
            $stmt->bindValue(":counter", $this->counter, PDO::PARAM_STR);
            $stmt->bindValue(":id", $this->id, PDO::PARAM_STR);
            
            
            DB::getConn()->beginTransaction();
            $stmt->execute();
            DB::getConn()->commit();
        }
    }
    
    public static function find(){
        $stmt = DB::getConn()->prepare('select id, ip, letter, counter from pc');
        $stmt->execute();

        $list = array();
        if($stmt->rowCount() > 0){
            foreach($stmt->fetchAll() as $row){
                $list[] = new PC($row['id'], $row['ip'], $row['letter'], $row['counter']);
            }
        }
        return $list;
    }
    
    public static function search($ip){
        $stmt = DB::getConn()->prepare("select id, ip, letter, counter from pc where ip = :ip");
        $stmt->bindValue(":ip", $ip);
        $stmt->execute();
        
        $res = $stmt->fetch();

        if ($res === false) {
            return new PC(null, $ip, null, 0);
        }

        return new PC($res["id"], $res["ip"], $res["letter"], $res["counter"]);
    }
    
    /**
     * Update the current PC
     */
    public function update(){
        $stmt = DB::getConn()->prepare("update pc 
                                           set ip      = :ip,
                                               letter  = :letter,
                                               counter = :counter
                                         where id      = :id");
        $stmt->bindValue(":ip", $this->ip);
        $stmt->bindValue(":letter", $this->letter);
        $stmt->bindValue(":counter", $this->counter);
        $stmt->bindValue(":id", $this->id);
        
        $stmt->execute();
    }
    
    /**
     * Get the next counter value
     * @return int
     */
    public function getNextCounter(){
        $stmt = DB::getConn()->prepare("select counter+1 as counter from pc where id = :id");
        $stmt->bindValue(":id", $this->id);
        $stmt->execute();
        
        $result = $stmt->fetch();
        if ($result) {
            return $result["counter"];
        } else {
            return 1; // Or handle the error in another way
        }
    }
}