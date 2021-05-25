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
            $connection = Configuration::closeConnection();
        }

        return $results;
    }

    public function updateInventory($options) {

        $options = json_decode($options, true);
        // Converts options to an array if only 1 option was selected.
        $options = is_array($options) ? $options : array($options);
        
        $result = false;

        try {

            $connection = Configuration::openConnection();

            foreach ($options as $option) {
                $statement = $connection->prepare("UPDATE orderOptions SET inventory=(inventory-1) WHERE id=:optionId");
                $statement->bindParam(":optionId", $option, PDO::PARAM_INT);
                $result = $statement->execute();
            }
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

    public function registerSponsor($formData) {
        
        $sponsor = json_decode($formData, false);

        $result = false;
        $lineItems = array();

        try {
            $connection = Configuration::openConnection();

            $sponsorships = is_array($sponsor->sponsorships) ? $sponsor->sponsorships : array($sponsor->sponsorships);
            $sponsorships = json_encode($sponsorships);
            
            $statement = $connection->prepare("INSERT INTO sponsors (`companyName`, `contactName`, `emailAddress`, `contactPhone`, `streetAddress`, `city`, `state`, `zipcode`, `companyUrl`, `services`, `sponsorships`) 
            VALUES (:companyName, :contactName, :emailAddress, :contactPhone, :streetAddress, :city, :stateAbbreviation, :zipcode, :companyUrl, :services, :sponsorships)");
            $statement->bindParam(":companyName", $sponsor->companyName, PDO::PARAM_STR);
            $statement->bindParam(":contactName", $sponsor->contactName, PDO::PARAM_STR);
            $statement->bindParam(":emailAddress", $sponsor->emailAddress, PDO::PARAM_STR);
            $statement->bindParam(":contactPhone", $sponsor->contactPhone, PDO::PARAM_STR);
            $statement->bindParam(":streetAddress", $sponsor->streetAddress, PDO::PARAM_STR);
            $statement->bindParam(":city", $sponsor->city, PDO::PARAM_STR);
            $statement->bindParam(":stateAbbreviation", $sponsor->state, PDO::PARAM_STR);
            $statement->bindParam(":zipcode", $sponsor->zipcode, PDO::PARAM_STR);
            $statement->bindParam(":companyUrl", $sponsor->companyUrl, PDO::PARAM_STR);
            $statement->bindParam(":services", $sponsor->services, PDO::PARAM_STR);
            $statement->bindParam(":sponsorships", $sponsorships, PDO::PARAM_STR);
            $result = $statement->execute();
            // Billing Information
            $billingBusiness = array();
            $billingBusiness['name'] = $sponsor->companyName;
            $billingBusiness['streetAddress'] = $sponsor->streetAddress;
            $billingBusiness['city'] = $sponsor->city;
            $billingBusiness['stateAbbreviation'] = $sponsor->state;
            $billingBusiness['zipcode'] = $sponsor->zipcode;

            $lineItems['billing'] = array("billingEmailAddress" => $sponsor->emailAddress, "billingBusiness" => $billingBusiness);

            $options = json_decode($sponsor->sponsorships, false);
            // Converts options to an array if only 1 option was selected.
            $options = is_array($options) ? $options : array($options);

            $lineItems['lineItems'] = array();
            foreach ($options as $option) {

                $statement = $connection->prepare("SELECT * FROM orderOptions WHERE id=:optionId");
                $statement->bindParam(":optionId", $option, PDO::PARAM_INT);
                $statement->execute();
                $result = $statement->fetch(PDO::FETCH_ASSOC);

                error_log("Line: " . __LINE__ . " " . date('Y-m-d H:i:s') . " option " . $result['description'] . "\n", 3, "/var/www/html/php-errors.log");

                $itemDescription = "Contact Name: " . $sponsor->contactName;    
                array_push($lineItems['lineItems'], array("quantity" => 1, "itemId" => $result['id'], "itemName" => $result['description'], "itemDescription" => $itemDescription, "price" => $result['price']));
            }

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

        return json_encode($lineItems, JSON_PRETTY_PRINT);
    }

}
?>