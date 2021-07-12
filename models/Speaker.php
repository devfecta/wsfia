<?php

require_once('User.php');

class Speaker extends User {
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


    public function registerSpeaker($formData) {
        
        $speaker = json_decode($formData, false);

        $result = false;

        try {
            $connection = Configuration::openConnection();
            
            $statement = $connection->prepare("INSERT INTO speakers (`fullName`, `phoneNumber`, `emailAddress`, `streetAddress`, `city`, `stateAbbreviation`, `zipcode`, `shortBio`, `classTitle`, `classDescription`, `specialEquipment`, `speakerFee`, `travelExpenses`, `hotelNights`, `meals`, `miscExpenses`) 
            VALUES (:fullName, :phoneNumber, :emailAddress, :streetAddress, :city, :stateAbbreviation, :zipcode, :shortBio, :classTitle, :classDescription, :specialEquipment, :speakerFee, :travelExpenses, :hotelNights, :meals, :miscExpenses)");
            $statement->bindParam(":fullName", $speaker->fullName, PDO::PARAM_STR);
            $statement->bindParam(":phoneNumber", $speaker->phoneNumber, PDO::PARAM_STR);
            $statement->bindParam(":emailAddress", $speaker->emailAddress, PDO::PARAM_STR);
            $statement->bindParam(":streetAddress", $speaker->streetAddress, PDO::PARAM_STR);
            $statement->bindParam(":city", $speaker->city, PDO::PARAM_STR);
            $statement->bindParam(":stateAbbreviation", $speaker->stateAbbreviation, PDO::PARAM_STR);
            $statement->bindParam(":zipcode", $speaker->zipcode, PDO::PARAM_STR);
            $statement->bindParam(":shortBio", $speaker->shortBio, PDO::PARAM_STR);
            $statement->bindParam(":classTitle", $speaker->classTitle, PDO::PARAM_STR);
            $statement->bindParam(":classDescription", $speaker->classDescription, PDO::PARAM_STR);
            $statement->bindParam(":specialEquipment", $speaker->specialEquipment, PDO::PARAM_STR);
            $statement->bindParam(":speakerFee", $speaker->speakerFee, PDO::PARAM_STR);
            $statement->bindParam(":travelExpenses", $speaker->travelExpenses, PDO::PARAM_STR);
            $statement->bindParam(":hotelNights", $speaker->hotelNights, PDO::PARAM_STR);
            $statement->bindParam(":meals", $speaker->meals, PDO::PARAM_STR);
            $statement->bindParam(":miscExpenses", $speaker->miscExpenses, PDO::PARAM_STR);
            $result = $statement->execute();
            

        }
        catch (PDOException $e) { 
            error_log("Line: " . __LINE__ . " " . date('Y-m-d H:i:s') . " " . $e->getMessage() . "\n", 3, "/var/www/html/php-errors.log");
        }
        catch (Exception $e) {
            error_log("Line: " . __LINE__ . " " . date('Y-m-d H:i:s') . " " . $e->getMessage() . "\n", 3, "/var/www/html/php-errors.log");
        }
        finally {
            $connection = Configuration::closeConnection();
        }

        return $result;
    }
}
?>