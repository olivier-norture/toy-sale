<?php
namespace classes\pages;

use classes\db\object\Objet;
use classes\utils\Logger;

class Basket {
    /**
     * Customer's basket
     * @var Objet[] 
     */
    private $basket;
    
    public function __construct() {
        $this->basket = array();
    }
    
    /**
     * Adds an objet to the basket.
     * @param Objet $objet 
     */
    public function add($objet){
        if(!$objet->isEmpty()){
            Logger::log("basket", "DEBUG", "Add Object $objet into basket");
            $this->basket[] = $objet;
        }
    }
    
    /**
     * Adds an array of objets to the basket
     * @param Objet[] $objets
     */
    public function addAll($objets){
        foreach($objets as $objet){
            $this->add($objet);
        }
    }
    
    /**
     * Removes an object to the basket from this id.
     * @param int $id
     */
    public function del($id){
        $idx = $this->getIndex($id);
        if($idx >= 0){
            $newBasket = array();
            foreach($this->basket as $objet){
                if($objet->getPk() != $id)
                    $newBasket[] = $objet;
            }
            $this->basket = $newBasket;
        }
    }
    
    /**
     * Update an objet of the basket. ID must haven't change.
     * @param Objet The updated object.
     */
    public function update($objet){
        $idx = $this->getIndex($objet->getPk());
        $this->basket[$idx] = $objet;
    }
    
    /**
     * Clear the basket.
     */
    public function clear(){
        $this->basket = array();
    }
    
    /**
     * Return true if the object exists.
     * @param type $id
     * @return boolean true if the object exists, otherwise false
     */
    public function contains($id){
        return $this->getIndex($id) >= 0;
    }
    
    /**
     * Get the index of the objet in the basket.
     * @param type $id
     * @return int 
     */
    private function getIndex($id){
        $idx = -1;
        for($i = 0; $i < sizeof($this->basket); $i++){
            if($this->basket[$i]->getPk() == $id){
                $idx = $i;
                $i = sizeof($this->basket);
            }
        }
        Logger::log("basket", "DEBUG", "Object's id '$id' at idx '$idx'");
        return $idx;
    }
    
    /**
     * Return an array with all object of the basket
     * @return Objet[]
     */
    public function getAll(){
        return $this->basket;
    }
    
    /**
     * Return the object in the given index
     * @param int $idx Index in the table
     */
    public function get($idx){
        return $this->basket[$this->getIndex($idx)];
    }
    
    /**
     * Get the basket's size
     * @return int size
     */
    public function size(){
        return sizeof($this->basket);
    }
    
    /**
     * Get the basket's total price
     * @return int total price
     */
    public function getTotalPrice(){
        $sum = 0;
        foreach ($this->basket as $objet){
            $sum += $objet->getPrix();
        }
        return $sum;
    }
    
    public function __toString() {
        return "";
    }
}
