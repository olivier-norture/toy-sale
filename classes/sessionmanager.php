<?php
namespace classes;

use classes\pages\Basket;
use classes\db\object\Participant;
use classes\db\object\User;
use classes\db\object\Bill;
use classes\utils\Session;

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

class SessionManager{
    public static $PARTICIPANT = "participant";
    public static $BASKET = "basket";
    public static $REDACTEUR = "redacteur";
    public static $BILL = "bill";
    public static $PC = "pc";
    public static $USER = "user";
    public static $USER_EDIT = "user_edit";
    
    /**
     * Return the current session's ID
     * @return int Session's ID
     */
    public function getId(){
        if(!empty($_SESSION['id'])){
            return $_SESSION['id'];
        }
    }
    
    /**
     * Save the given participant in session
     * @param Participant $participant
     */
    public function saveParticipant($participant){
        Session::set(SessionManager::$PARTICIPANT, $participant);
    }
    
    /**
     * Retrieves the participant stored in the session
     * @return Participant
     */
    public function getParticipant(){
        return Session::get(SessionManager::$PARTICIPANT) == null ? new Participant() : Session::get(SessionManager::$PARTICIPANT);
    }
    
    /**
     * Clear the participant stored in the session
     */
    public function clearParticipant(){
        Session::remove(SessionManager::$PARTICIPANT);
    }
    
    /**
     * Save the given redacteur in session
     * @param Participant $redacteur
     */
    public function saveRedacteur($redacteur){
        Session::set(SessionManager::$REDACTEUR, $redacteur);
    }
    
    /**
     * Retrieves the participant stored in the session
     * @return Participant
     */
    public function getRedacteur(){
        return Session::get(SessionManager::$REDACTEUR) == null ? new Participant() : Session::get(SessionManager::$REDACTEUR);
    }
    
      /**
     * Clear the redacteur stored in the session
     */
    public function clearRedacteur(){
        Session::remove(SessionManager::$REDACTEUR);
    }
    
     /**
     * Save the given user in session
     * @param User $user
     */
    public function saveUser($user){
        Session::set(SessionManager::$USER, $user);
    }
    
    /**
     * Retrieves the user stored in the session
     * @return User
     */
    public function getUser(){
        return Session::get(SessionManager::$USER) == null ? new User() : Session::get(SessionManager::$USER);
    }
    
      /**
     * Clear the user stored in the session
     */
    public function clearUser(){
        Session::remove(SessionManager::$USER);
    }
    
    /**
     * Save the given basket in session
     * @param Basket $basket The basket to save in session
     */
    public function saveBasket($basket){
        Session::set(SessionManager::$BASKET, $basket);
    }
    
    /**
     * Retrieves the basket stored in the current session
     * @return Basket The basket stored in the session
     */
    public function getBasket(){
        return Session::get(SessionManager::$BASKET) != null ? Session::get(SessionManager::$BASKET) : new Basket();
    }
    
    /**
     * Clear the current basket
     */
    public function clearBasket(){
        Session::remove(SessionManager::$BASKET);
    }
    
    public function getBill(){
        return Session::get(SessionManager::$BILL) != null ? Session::get(SessionManager::$BILL) : new Bill(null, null , 0, 0, 0);
    }
    
    public function saveBill($bill){
        Session::set(SessionManager::$BILL, $bill);
    }
    
    public function clearBill(){
        Session::remove(SessionManager::$BILL);
    }
    
    public function getPc(){
        return Session::get(SessionManager::$PC);
    }
    
    public function savePc($pc){
        Session::set(SessionManager::$PC, $pc);
    }
    
    public function clearPc(){
        Session::remove(SessionManager::$PC);
    }
    
    public function getUserEdit(){
        return Session::get(SessionManager::$USER_EDIT) != null ? Session::get(SessionManager::$USER_EDIT) : new User();
    }
    
    public function saveUserEdit($user){
        Session::set(SessionManager::$USER_EDIT, $user);
    }
    
    public function clearUserEdit(){
        Session::remove(SessionManager::$USER_EDIT);
    }
}