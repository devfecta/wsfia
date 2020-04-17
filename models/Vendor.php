<?php
require_once('User.php');

class Vendor extends User {
    /**
     * During the registration process the id will be created using 
     * the table row ID and sinceDate (Created Date). 
     * Example: WSFIA-VS-320190101 (ID Year Month Day)
     */
    private $id; // Vendor/Sponsor ID
    private $jobTitle;
    private $companies = array();
    private $representatives = array();

    public function __construct($id) {

        // Add SQL statement to set the value for the setters.

    }
    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
    /**
     * Get the value of jobTitle
     */ 
    public function getJobTitle()
    {
        return $this->jobTitle;
    }
    /**
     * Set the value of jobTitle
     *
     * @return  self
     */ 
    public function setJobTitle($jobTitle)
    {
        $this->jobTitle = $jobTitle;

        return $this;
    }
    /**
     * Get the value of company
     */ 
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set the value of company
     * Return an array with the ID and company information
     * @return  self
     */ 
    public function setCompany($company)
    {
        $this->company = $company;

        return $this;
    }
    /**
     * Get the value of representatives
     */ 
    public function getRepresentatives()
    {
        return $this->representatives;
    }

    /**
     * Set the value of representatives
     * Return an array with the representative name
     * @return  self
     */ 
    public function setRepresentatives($representativesJSON)
    {
        /**
         * JSON is stored in the database and then converted to an array allowing for 
         * one vendor to have multiple representatives.
         * How the JSON is stored in the database:
         * $representativesJSON = '[{"name":"Bill Smith"},{"name":"Sue Johnson"}]';
         */
        $this->representatives = json_decode($representativesJSON);

        return $this;
    }

}
?>