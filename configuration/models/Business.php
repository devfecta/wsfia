<?php
require_once('Member.php');

class Business {

    private $id;
    private $name;
    private $station;
    private $streetAddress;
    private $city;
    private $state = array();
    private $zipcode;
    private $phone;
    private $url;
    private $services = array();
    private $type = array();

    public function __construct($id) {

        //$statement = Configuration::openConnection()->prepare("SELECT * FROM businesses AS b JOIN states AS s ON b.state=s.stateId, businessTypes AS bt WHERE b.id=:id AND b.type=bt.typeId");
        $statement = Configuration::openConnection()->prepare("SELECT * FROM businesses AS b JOIN states AS s ON b.state=s.stateId WHERE b.id=:id");
        
        $statement->bindParam(":id", $id);

        $statement->execute();

        $results = $statement->fetch(PDO::FETCH_ASSOC);

        $this->setBusinessId($results['id']);
        $this->setName($results['name']);
        $this->setStation($results['station']);
        $this->setStreetAddress($results['streetAddress']);
        $this->setCity($results['city']);
        $this->setState(array('id' =>$results['stateId'], 'abbreviation' => $results['stateAbbreviation'], 'name' => $results['stateName']));
        $this->setZipcode($results['zipcode']);
        $this->setPhone($results['phone']);
        $this->setUrl($results['url']);
        $this->setServices($results['services']);
        //$this->setType(array('id' => $results['typeId'], 'type' => $results['typeName']));

        Configuration::closeConnection();
    }

    public function __toString() 
    {
        $json = array(
            "id" => $this->id,
            "name" => $this->name,
            "station" => $this->station,
            "streetAddress" => $this->streetAddress,
            "city" => $this->city,
            "state" => $this->state,
            "zipcode" => $this->zipcode,
            "phone" => $this->phone,
            "url" => $this->url,
            "services" => $this->services
            //, "type" => $this->type
        );

        return json_encode($json, JSON_PRETTY_PRINT);
    }

    /**
     * Get the value of id
     */ 
    public function getBusinessId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setBusinessId($id)
    {
        $this->id = $id;
    }

    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get the value of station
     */ 
    public function getStation()
    {
        return $this->station;
    }

    /**
     * Set the value of station
     *
     * @return  self
     */ 
    public function setStation($station)
    {
        $this->station = $station;
    }

    /**
     * Get the value of streetAddress
     */ 
    public function getStreetAddress()
    {
        return $this->streetAddress;
    }

    /**
     * Set the value of streetAddress
     *
     * @return  self
     */ 
    public function setStreetAddress($streetAddress)
    {
        $this->streetAddress = $streetAddress;
    }

    /**
     * Get the value of city
     */ 
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set the value of city
     *
     * @return  self
     */ 
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * Get the value of state
     */ 
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set the value of state
     * Return an array with the ID, abbreviation, and name
     * @return  self
     */ 
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * Get the value of zipcode
     */ 
    public function getZipcode()
    {
        return $this->zipcode;
    }

    /**
     * Set the value of zipcode
     *
     * @return  self
     */ 
    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;
    }

    /**
     * Get the value of phone
     */ 
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set the value of phone
     *
     * @return  self
     */ 
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * Get the value of url
     */ 
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set the value of url
     * @return  self
     */ 
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Get the value of services
     */ 
    public function getServices()
    {
        return $this->services;
    }

    /**
     * Set the value of services
     * Return an array with the service ID
     * @return  self
     */ 
    public function setServices($services)
    {
        $this->services = $services;
    }

    /**
     * Get the value of type
     */ 
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the value of type
     * Return an array with the ID and type name
     * @return  self
     */ 
    public function setType($type)
    {
        $this->type = $type;
    }

    public function getBusinesses() {
        /**
         * Returns information on all businesses in JSON
         */
        try {
            $statement = Configuration::openConnection()->prepare("SELECT * FROM businesses AS b JOIN states AS s ON b.state=s.stateId, businessTypes AS bt WHERE b.type=bt.typeId");
            $statement->execute();

            $results = $statement->fetchAll(PDO::FETCH_COLUMN);

            $businesses = array();

            //echo ('test': 'test2');
            
            foreach ($results as $index => $id) {
                $business = new Business($id);
                array_push($businesses, json_decode($business));
            }

            Configuration::closeConnection();
        }
        catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
        $businesses = json_encode($businesses, JSON_PRETTY_PRINT);

        return $businesses;
    }

    public function searchBusinessesByName($businessName) {
        /**
         * Returns information on all businesses in JSON
         */
        $businesses = array();
        
        try {
            $statement = Configuration::openConnection()->prepare("SELECT * FROM businesses AS b JOIN states AS s ON b.state=s.stateId WHERE b.name LIKE :businessName");
            $statement->bindValue(":businessName", '%'.$businessName.'%');
            $statement->execute();

            $results = $statement->fetchAll(PDO::FETCH_COLUMN);
            
            foreach ($results as $index => $id) {

                $business = new Business($id);

                array_push($businesses, json_decode($business));
                
            }

            Configuration::closeConnection();
        }
        catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }

        
        $businesses = json_encode($businesses, JSON_PRETTY_PRINT);

        return $businesses;

    }

    /**
     * Inserts a new business into the database.
     * @param array $companyInfo An array of business information.
     * @return boolean Returns JSON true/false if the insert statement was successful. 
     */
    public function createBusiness($companyInfo) {
        
        $data = json_decode(json_encode($companyInfo), FALSE);

        $connection = Configuration::openConnection();
        $statement = $connection->prepare("INSERT INTO businesses (`name`, `station`, `streetAddress`, `city`, `state`, `zipcode`, `phone`, `url`, `services`, `type`) VALUES (:name, :station, :streetAddress, :city, :state, :zipcode, :phone, :url, :services, :type)");

        $statement->bindParam(":name", $data->businessName);
        $statement->bindParam(":station", $data->station);
        $statement->bindParam(":streetAddress", $data->streetAddress);
        $statement->bindParam(":city", $data->city);
        $statement->bindParam(":state", $data->states);
        $statement->bindParam(":zipcode", $data->zipcode);
        $statement->bindParam(":phone", $data->phone);
        $statement->bindParam(":url", $data->url);
        $statement->bindParam(":services", $data->services);
        $statement->bindParam(":type", $data->departmentType);

        return json_encode($statement->execute(), JSON_PRETTY_PRINT);
    }
    /**
     * @return object Returns JSON information on all states in JSON
     */
    public function getStates() {
        
        $states = array();
        
        try {
            $statement = Configuration::openConnection()->prepare("SELECT * FROM states ORDER BY stateName");
            $statement->execute();

            $results = $statement->fetchAll(PDO::FETCH_ASSOC);

            Configuration::closeConnection();
        }
        catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }

        $states = json_encode($results, JSON_PRETTY_PRINT);

        return $states;

    }
}
?>