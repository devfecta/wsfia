<?php
//require_once('../WSFIA.php');

class User {

    private $userId;
    private $type = array(); // ID and Member, Vendor, Sponsor, Vendor & Sponsor, Speaker
    private $password;
    private $firstName;
    private $lastName;
    private $emailAddress;
    private $lastLoginDate;
    
    public function __construct($id) 
    {
        $statement = Configuration::openConnection()->prepare("SELECT * FROM users WHERE id=:id");
        $statement->bindParam(":id", $id);
        $statement->execute();

        $results = $statement->fetch(PDO::FETCH_ASSOC);

        $this->setUserId($results['id']);
        $this->setUserType($results['type']);
        $this->setPassword($results['password']);
        $this->setFirstName($results['firstName']);
        $this->setLastName($results['lastName']);
        $this->setEmailAddress($results['emailAddress']);
        $this->setLastLoginDate($results['lastLoginDate']);

        Configuration::closeConnection();
/*
       echo get_class(Configuration::openConnection());
*/

    }

    public function __toString() {

        $json = array(
            "userId" => $this->userId,
            "type" => $this->type,
            "password" => $this->password,
            "firstName" => $this->firstName,
            "lastName" => $this->lastName,
            "emailAddress" => $this->emailAddress,
            "lastLoginDate" => $this->lastLoginDate
        );

        return json_encode($json, JSON_PRETTY_PRINT);

    }

    /**
     * Get the value of userId
     */ 
    public function getUserId()
    {
        return $this->userId;
    }
    /**
     * Set the value of userId
     * 
     * @return  self
     */ 
    public function setUserId($id)
    {
        $this->userId = $id;
    }

    /**
     * Get the value of type
     */ 
    public function getUserType()
    {
        return $this->type;
    }
    /**
     * Set the value of type
     * Return an array with the ID and type name
     * @return  self
     */ 
    public function setUserType($typesJSON)
    {
        $typesArray = json_decode($typesJSON, true);

        $this->type = $typesArray;
        /*
        foreach ($typesArray as $id => $type) {
            array_push($this->type, 
                array(
                    'id' => $id,
                    'type' => $type
                )
            );
        }
        */
    }
    /**
     * Get the value of password
     */ 
    public function getPassword()
    {
        return $this->password;
    }
    /**
     * Set the value of password
     *
     * @return  self
     */ 
    public function setPassword($id)
    {
        $this->password = $id;
    }
    /**
     * Get the value of firstName
     */ 
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set the value of firstName
     *
     * @return  self
     */ 
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * Get the value of lastName
     */ 
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set the value of lastName
     *
     * @return  self
     */ 
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * Get the value of emailAddress
     */ 
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * Set the value of emailAddress
     *
     * @return  self
     */ 
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;
    }

    /**
     * Get the value of lastLoginDate
     */ 
    public function getLastLoginDate()
    {
        return $this->lastLoginDate;
    }

    /**
     * Set the value of lastLoginDate
     *
     * @return  self
     */ 
    public function setLastLoginDate($lastLoginDate)
    {
        $this->lastLoginDate = date('Y-m-d', strtotime($lastLoginDate));
    }
}
?>