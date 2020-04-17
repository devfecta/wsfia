<?php

interface iRegistration {
    /**
     * Gets information about a session
     */ 
    public function getSession();
    /**
     * Sets the value of the session variable, and insert/updates the current session data
     */ 
    public function setSession($sessionData);
    /**
     * Register new Member, Vendor/Sponsor, or Speaker
     */ 
    public function register($user);
    /**
     * Remove registration for a Member, Vendor/Sponsor, or Speaker
     */
    public function unRegister($member);
    /**
     * Update registration for a Member, Vendor/Sponsor, or Speaker
     */
    public function updateRegistration($member);
    /**
     * Create a report of current Members, Vendor/Sponsors, or Speakers registrations
     */
    public function reportRegistrations();
    /**
     * Create a report of a current Member, Vendor/Sponsor, or Speaker registration
     */
    public function reportRegistration($member);

}

?>