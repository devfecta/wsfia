<?php
require_once('User.php');
require_once('Business.php');

class Member extends User {
    /**
     * id replaces the username. During the registration process the id 
     * will be created using the table row ID and sinceDate. 
     * Example: WSFIA-320190101 (User ID Year Month Day)
     */
    protected $user;
    
    private $id; // Same as a username
    private $jobTitle;
    private $departments = array();
    private $areas = array();
    private $expirationDate;
    private $status = array();
    private $sinceDate;
    private $studentId;
    
    public function __construct($id) {

        $statement = Configuration::openConnection()->prepare("SELECT * FROM users AS u INNER JOIN members AS m ON u.id=m.userId, statuses AS s WHERE u.id=:id AND s.statusId=m.status");
        $statement->bindParam(":id", $id);
        $statement->execute();

        $results = $statement->fetch(PDO::FETCH_ASSOC);

        $this->setSinceDate($results['sinceDate']);
        $this->setMemberId($results['id']);
        $this->setJobTitle($results['jobTitle']);
        $this->setDepartments($results['departments']);
        $this->setMemberAreas($results['areas']);
        $this->setMembershipExpirationDate($results['expirationDate']);
        $this->setStatus(array('id' => $results['statusId'], 'status' => $results['statusName']));
        $this->setSinceDate($results['sinceDate']);
        $this->setStudentId($results['studentId']);

        $this->user = new User($results['userId']);

        Configuration::closeConnection();
        // return json_encode($this, JSON_PRETTY_PRINT);

    }
    
    public function __toString() 
    {
        $json = array(
            "user" => json_decode($this->user),
            "id" => $this->id,
            "jobTitle" => $this->jobTitle,
            "departments" => $this->departments,
            "areas" => $this->areas,
            "expirationDate" => $this->expirationDate,
            "status" => $this->status,
            "sinceDate" => $this->sinceDate,
            "studentId" => $this->studentId
        );

        return json_encode($json, JSON_PRETTY_PRINT);
    }

    /**
     * Get the value of id
     */ 
    public function getMemberId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setMemberId($id)
    {   
        $this->id = $id;
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
    }
    /**
     * Get the value of departments
     */ 
    public function getDepartments()
    {
        return $this->departments;
    }

    /**
     * Set the value of departments
     * Return an array with the ID and department information
     * @return  self
     */ 
    public function setDepartments($departmentsJSON)
    {
        /**
         * JSON is stored in the database and then converted to an array allowing for 
         * one member to have multiple departments.
         * How the JSON is stored in the database:
         * $departmentsJSON = '[1,3]';
         */

        //$this->departments = json_decode($departmentsJSON, true);
        
        $departmentsArray = json_decode($departmentsJSON, true);

        foreach ($departmentsArray as $id) {
            
            $department = new Business($id);

            array_push($this->departments, 
                array(
                    'id' => $department->getBusinessId(),
                    'name' => $department->getName(),
                    'station' => $department->getStation(),
                    'streetAddress' => $department->getStreetAddress(),
                    'city' => $department->getCity(),
                    'state' => $department->getState(),
                    'zipcode' => $department->getZipcode(),
                    'phone' => $department->getPhone(),
                    'type' => $department->getType()
                )
            );
            
        }
        
    }

    /**
     * Get the value of areas
     */ 
    public function getMemberAreas()
    {
        return $this->areas;
    }

    /**
     * Set the value of areas
     * Return an array with the ID and area information
     * @return  self
     */ 
    public function setMemberAreas($areasJSON)
    {
        /**
         * JSON is stored in the database and then converted to an array allowing for 
         * one member to have multiple areas.
         * How the JSON is stored in the database:
         * $areasJSON = {"1":"Area 1", "2":"Area 2"};
         */
        $this->areas = json_decode($areasJSON, true);
        /*
        $areasArray = json_decode($areasJSON, true);

        foreach ($areasArray as $id => $area) {
            array_push($this->areas, 
                array(
                    'id' => $id,
                    'area' => $area
                )
            );
        }
        */
    }

    /**
     * Get the value of expirationDate
     */ 
    public function getMembershipExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * Set the value of expirationDate
     *
     * @return  self
     */ 
    public function setMembershipExpirationDate($expirationDate)
    {
        $this->expirationDate = date('Y-m-d', strtotime($expirationDate));
    }

    /**
     * Get the value of status
     */ 
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the value of status
     * Return an array with the ID and status name
     * @return  self
     */ 
    public function setStatus($status)
    {
        $this->status = $status;
    }
    
    /**
     * Get the value of sinceDate
     */ 
    public function getSinceDate()
    {
        return $this->sinceDate;
    }
    /**
     * Set the value of sinceDate
     *
     * @return  self
     */ 
    public function setSinceDate($sinceDate)
    {
        $this->sinceDate = date('Y-m-d', strtotime($sinceDate));
    }
    /**
     * Get the value of studentId
     */ 
    public function getStudentId()
    {
        return $this->studentId;
    }

    /**
     * Set the value of studentId
     *
     * @return  self
     */ 
    public function setStudentId($studentId)
    {
        $this->studentId = $studentId;
    }
    /**
     * Get get all members based on the value of businessId
     */ 
    public function getMembersByBusiness($businessId) {

        $members = array();
        
        try {
            
            $statement = Configuration::openConnection()->prepare("SELECT * FROM `members` WHERE JSON_CONTAINS(`departments`, :businessId)");
            $statement->bindValue(":businessId", json_encode(array("id"=>$businessId)));
            $statement->execute();

            $results = $statement->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($results as $result) {

                $member = new Member($result['userId']);

                array_push($members, json_decode($member));
                
            }
            
            Configuration::closeConnection();
        }
        catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
        //$members = json_encode($members, JSON_PRETTY_PRINT);
        //stripslashes(json_decode($results));
        //return json_encode($results, JSON_PRETTY_PRINT);

        $members = json_encode($members, JSON_PRETTY_PRINT);

        return $members;
    }

}
?>