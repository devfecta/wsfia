<?php
require_once('Vendor.php');
require_once('./interfaces/iRegistration.php');
/**
 * Handles the conference registration for WSFIA vendors and sponsors.
 * Extends Vendor which extends User.
 */
class RegisterConferenceVendor extends Vendor implements iRegistration {

    public function __construct($member) {}

    public function getSession() {
        return $this->sessionData;
    }
    
    public function setSession($sessionData) {}

    public function register($member) {
        
        //return true;
    }
        
    public function unRegister($member) {}
        
    public function updateRegistration($member) {}
        
    public function sendConfirmation($member) {}
        
    public function reportRegistrations() {}

    public function reportRegistration($member) {}

}

?>