<?php

class Sponsor {
    /**
     * During the registration process the id will be created using 
     * the table row ID and sinceDate (Created Date). 
     * Example: WSFIA-S-320190101 (ID Year Month Day)
     */
    private $id; // Speaker ID
    private $jobTitle;
    private $companies = array();
    private $bio;
    private $photo;

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
     * Get the value of bio
     */ 
    public function getBio()
    {
        return $this->bio;
    }

    /**
     * Set the value of bio
     *
     * @return  self
     */ 
    public function setBio($bio)
    {
        $this->bio = $bio;

        return $this;
    }

    /**
     * Get the value of photo
     */ 
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set the value of photo
     *
     * @return  self
     */ 
    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    public function getInventory($optionType) {

        $results = false;

        try {
            $connection = Configuration::openConnection();

            $statement = $connection->prepare("SELECT * FROM orderOptions WHERE type=:optionType");
            $statement->bindParam(":optionType", $optionType, PDO::PARAM_STR);
            $statement->execute();

            $results = $statement->fetchAll(PDO::FETCH_ASSOC);

            $results = json_encode($results, JSON_PRETTY_PRINT);
        }
        catch (PDOException $e) { 
            error_log("Line: " . __LINE__ . " " . date('Y-m-d H:i:s') . " " . $e->getMessage() . "\n", 3, "/var/www/html/php-errors.log");
        }
        catch (Exception $e) {
            error_log("Line: " . __LINE__ . " " . date('Y-m-d H:i:s') . " " . $e->getMessage() . "\n", 3, "/var/www/html/php-errors.log");
        }
        finally {
            Configuration::closeConnection();
        }

        return $results;
    }
}
?>