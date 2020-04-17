<?php
require_once('Membership.php');
require_once('./interfaces/iRegistration.php');
/**
 * Handles the conference registration for WSFIA members.
 * Extends Member which extends User.
 */
class RegisterConferenceMember extends Membership implements iRegistration {

    public function __construct() {}

    public function getSession() {
        return $this->sessionData;
    }
    
    public function setSession($sessionData) {}

    public function register($order) {

        $newMember = false;

        if (isset($order['userId'])) {
            $userId = $order['userId'];
        } else {
            $registerMembership = new Membership();
            $userId = json_decode($registerMembership->register($order))->response;

            $newMember = true;
        }

        $member = new Member($userId);

        $processOrder = new ProcessOrder();

        if ($newMember) {

            $processOrder->addLineItem($order);

        } else {}
        
        return $member;
    }
        
    public function unRegister($member) {}
        
    public function updateRegistration($member) {}
        
    public function sendConfirmation($member) {}
        
    public function reportRegistrations() {}

    public function reportRegistration($member) {}

}

?>