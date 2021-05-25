<?php

class Vendor {
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

    public function registerVendor($formData) {
        
        $vendor = json_decode($formData, false);

        $result = false;
        $lineItems = array();

        try {
            $connection = Configuration::openConnection();

            $booths = is_array($vendor->booths) ? $vendor->booths : array($vendor->booths);
            $booths = json_encode($booths);
            
            $statement = $connection->prepare("INSERT INTO vendors (`companyName`, `contactName`, `emailAddress`, `contactPhone`, `streetAddress`, `city`, `state`, `zipcode`, `companyUrl`, `services`, `representativeOne`, `representativeTwo`, `booths`) 
            VALUES (:companyName, :contactName, :emailAddress, :contactPhone, :streetAddress, :city, :stateAbbreviation, :zipcode, :companyUrl, :services, :representativeOne, :representativeTwo, :booths)");
            $statement->bindParam(":companyName", $vendor->companyName, PDO::PARAM_STR);
            $statement->bindParam(":contactName", $vendor->contactName, PDO::PARAM_STR);
            $statement->bindParam(":emailAddress", $vendor->emailAddress, PDO::PARAM_STR);
            $statement->bindParam(":contactPhone", $vendor->contactPhone, PDO::PARAM_STR);
            $statement->bindParam(":streetAddress", $vendor->streetAddress, PDO::PARAM_STR);
            $statement->bindParam(":city", $vendor->city, PDO::PARAM_STR);
            $statement->bindParam(":stateAbbreviation", $vendor->state, PDO::PARAM_STR);
            $statement->bindParam(":zipcode", $vendor->zipcode, PDO::PARAM_STR);
            $statement->bindParam(":companyUrl", $vendor->companyUrl, PDO::PARAM_STR);
            $statement->bindParam(":services", $vendor->services, PDO::PARAM_STR);

            $statement->bindParam(":representativeOne", $vendor->representativeOne, PDO::PARAM_STR);
            $statement->bindParam(":representativeTwo", $vendor->representativeTwo, PDO::PARAM_STR);

            $statement->bindParam(":booths", $booths, PDO::PARAM_STR);
            $result = $statement->execute();
            // Billing Information
            $billingBusiness = array();
            $billingBusiness['name'] = $vendor->companyName;
            $billingBusiness['streetAddress'] = $vendor->streetAddress;
            $billingBusiness['city'] = $vendor->city;
            $billingBusiness['stateAbbreviation'] = $vendor->state;
            $billingBusiness['zipcode'] = $vendor->zipcode;

            $lineItems['billing'] = array("billingEmailAddress" => $vendor->emailAddress, "billingBusiness" => $billingBusiness);

            $options = json_decode($vendor->booths, false);
            // Converts options to an array if only 1 option was selected.
            $options = is_array($options) ? $options : array($options);

            $lineItems['lineItems'] = array();
            foreach ($options as $option) {

                $statement = $connection->prepare("SELECT * FROM orderOptions WHERE id=:optionId");
                $statement->bindParam(":optionId", $option, PDO::PARAM_INT);
                $statement->execute();
                $result = $statement->fetch(PDO::FETCH_ASSOC);

                error_log("Line: " . __LINE__ . " " . date('Y-m-d H:i:s') . " option " . $result['description'] . "\n", 3, "/var/www/html/php-errors.log");

                $itemDescription = "Contact Name: " . $vendor->contactName . "\n Representatives: " . $vendor->representativeOne . " - " . $vendor->representativeTwo;    
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