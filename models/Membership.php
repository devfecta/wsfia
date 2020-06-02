<?php
require_once('Member.php');
require_once('./interfaces/iRegistration.php');
/**
 * Handles the membership registration for WSFIA members.
 * Extends Member which extends User.
 */
class Membership extends Member implements iRegistration {

    private $sessionData;

    public function __construct() {}

    public function getSession() {
        return $this->sessionData;
    }

    public function setSession($sessionData) {
        $this->sessionData = $sessionData;
    }
    
    public function addMember($sessionData) {

        //return json_encode($sessionData, JSON_PRETTY_PRINT);
        $data = json_decode(json_encode($sessionData), FALSE);

        $arr = null;

        foreach($data as $key => $value) {
            if ($key == 'sessionId' || $key == 'class' || $key == 'method') {}
            elseif ($key == 'areas' || $key == 'businesses') {
                $arr[$key] = explode(",", $value);
            }
            else {
                $arr[$key] = $value;
            }
            
        }

        $result = '';

        try{

            $connection = Configuration::openConnection();
            $statement = $connection->prepare("INSERT INTO userSessions (`sessionId`, `registration`) VALUES (:sessionId, :registration)");
            $statement->bindParam(":sessionId", $data->sessionId);
            $statement->bindParam(":registration", json_encode($arr));

            $result = json_encode($statement->execute(), JSON_PRETTY_PRINT);
        } catch (Exception $e) {
            
            $result = json_encode($e, JSON_PRETTY_PRINT);    

        }

        return $result;

    }

    public function getRegistrants($sessionId) {
        
        $connection = Configuration::openConnection();
        $statement = $connection->prepare("SELECT id, registration FROM userSessions WHERE sessionId=:sessionId");
        $statement->bindParam(":sessionId", $sessionId);
        $statement->execute();
        $results = $statement->fetchAll();
        // Array of registrants
        $registrants = array();

        $statementBusiness = $connection->prepare("SELECT * FROM businesses WHERE id=:businessId");

        foreach($results as $result) {

            $regId = $result['id'];

            $registration = json_decode($result['registration'], false);

            foreach($registration->areas as $index => $area) {
                $registration->areas[$index] = "Area $area";
            }

            array_push($registrants, $registration);
            $registrants = array_filter($registrants);

            foreach($registration->businesses as $index => $business) {
                

                $statementBusiness->bindParam(":businessId", $business);
                $statementBusiness->execute();
                $resultsBusiness = $statementBusiness->fetchAll(PDO::FETCH_ASSOC);

                $registration->businesses[$index] = $resultsBusiness[0];
            }
            
        }

        $connection = Configuration::closeConnection();

        return json_encode($registrants, JSON_PRETTY_PRINT);

    }

    public function login($formData) {
        $data = json_decode(json_encode($formData), FALSE);

        $userInfo = ["id" => 0, "authenticated" => false];

        /**
         * Returns the JSON with only user ID and authentication boolean
         */
        try {

            $connection = Configuration::openConnection();

            // Get Billing Business Information
            $statement = $connection->prepare("SELECT `users`.`id` AS `id`, `users`.`emailAddress` AS email, `users`.`password` AS `password`, `users`.`firstName` AS `firstName`, `members`.`id` AS `wsfiaId`, `members`.`expirationDate` AS `expirationDate`, `members`.`status` AS `status`, `members`.`studentId` AS `studentId` FROM `users` INNER JOIN `members` ON `members`.`userId`=`users`.`id` WHERE `users`.`emailAddress`=:emailAddress");
            $statement->bindParam(":emailAddress", $data->emailAddress);
            $statement->execute();

            if ($statement->rowCount() > 0) {
                $result = $statement->fetch(PDO::FETCH_ASSOC);

                $userInfo['id'] = $result['id'];
                $userInfo['wsfiaId'] = $result['wsfiaId'];
                $userInfo['firstName'] = $result['firstName'];
                $userInfo['authenticated'] = password_verify($data->password, $result['password']);
                $userInfo['expirationDate'] = date("Y-m-d", strtotime($result['expirationDate']));
                
                switch($result['status']) {
                    case 1:
                        $status = "Active";
                        break;
                    case 2:
                        $status = "Suspended";
                        break;
                    default:
                        $status = "Inactive";
                        break;
                }
                $userInfo['status'] = $status;

                $userInfo['studentId'] = $result['studentId'];
            }
            
            Configuration::closeConnection();

            return json_encode($userInfo, JSON_PRETTY_PRINT);

        }
        catch (PDOException $e) {
            //return "Error: " . $e->getMessage();
            return json_encode(array("error" => $e->getMessage()), JSON_PRETTY_PRINT);
        }

        return json_encode($userInfo, JSON_PRETTY_PRINT);
    }

    public function resetPassword($formData) {
        $data = json_decode(json_encode($formData), FALSE);
        $passwordUpdated["updatedPassword"] = false;
        /**
         * Returns the JSON with a boolean if the password was updated or not.
         */
        try {

            $connection = Configuration::openConnection();

            $statement = $connection->prepare("UPDATE `users` SET `password`=:password WHERE `emailAddress`=:emailAddress");
            $statement->bindParam(":emailAddress", $data->emailAddress);
            $statement->bindParam(":password", password_hash($data->password, PASSWORD_BCRYPT));
            $statement->execute();

            if ($statement->rowCount() > 0) {
                $passwordUpdated["updatedPassword"] = true;
            }
            
            Configuration::closeConnection();

        }
        catch (PDOException $e) {
            return $passwordUpdated["updatedPassword"] = $e->getMessage();
        }

        return json_encode($passwordUpdated, JSON_PRETTY_PRINT);
    }

    
    public function checkEmailAddress($emailAddress) {
        /**
         * Returns information on all businesses in JSON
         */
        try {

            $statement = Configuration::openConnection()->prepare("SELECT `emailAddress` FROM `users` WHERE `emailAddress`=:emailAddress");
            $statement->bindValue(":emailAddress", $emailAddress, PDO::PARAM_STR);
            $statement->execute();

            //$result = json_encode('{"result" : ' . $statement->rowCount() . '}', JSON_PRETTY_PRINT);
            $result = '{"result" : ' . $statement->rowCount() . '}';

            Configuration::closeConnection();
        }
        catch (PDOException $e) {
            //return "Error: " . $e->getMessage();
            $result = '{"result" : ' . $e->getMessage() . '}';
        }

        return $result;
    }

    public function register($sessionData) {

        $data = json_decode(json_encode($sessionData), FALSE);
        /**
         * Returns the new User ID in JSON
         */
        try {
            $registrants = json_decode($this->getRegistrants($data->sessionId));
            // $connection is lets you use the same connection for multiple statements.
            $connection = Configuration::openConnection();

            $lineItem = array();
            //$characters = "0123456789abcdefghijklmnopqrstuvwxyz!@#$%&";

            foreach($registrants as $registrant) {

                //$password = $characters[mt_rand(0, strlen($Characters))];
                $password = '123abc';
                // User Information
                $statement = $connection->prepare("INSERT INTO users (`type`, `password`, `firstName`, `lastName`, `emailAddress`) VALUES (:type, :password, :firstName, :lastName, :emailAddress)");
                $statement->bindParam(":type", json_encode(array(2=>"Member")));
                $statement->bindParam(":password", password_hash($password, PASSWORD_BCRYPT));
                $statement->bindParam(":firstName", $registrant->firstName);
                $statement->bindParam(":lastName", $registrant->lastName);
                $statement->bindParam(":emailAddress", $registrant->emailAddress);
                $statement->execute();

                $newUserId = $connection->lastInsertId();

                // Create member ID
                $statement = $connection->prepare("SELECT DATE_FORMAT(CURDATE(), '%Y-%m-%d')");
                $statement->execute();
                $dateCurrent = $statement->fetch(PDO::FETCH_COLUMN);
                $memberId = 'WSFIA-' . $newUserId . date('ynj', strtotime($dateCurrent));

                // Member Information
                $statement = $connection->prepare("INSERT INTO members (`id`, `userId`, `jobTitle`, `departments`, `areas`, `expirationDate`, `studentId`) VALUES (:id, :userId, :jobTitle, :departments, :areas, :expirationDate, :studentId)");
                $statement->bindParam(":id", $memberId);
                $statement->bindParam(":userId", $newUserId);
                $statement->bindParam(":jobTitle", $registrant->jobTitle);
                $statement->bindParam(":departments", json_encode($registrant->businesses));
                $statement->bindParam(":areas", json_encode($registrant->areas));
                // After October the expiration date is increased to the following year.
                if (strtotime($dateCurrent) < strtotime(date('Y-11-1'))) {
                    $statement->bindParam(":expirationDate", date('Y-12-31', strtotime($dateCurrent)));
                } else {
                    $statement->bindParam(":expirationDate", date('Y-12-31', strtotime($dateCurrent . '+1 year')));
                }
                $statement->bindParam(":studentId", $registrant->studentId);
                $statement->execute();

                // Create an order and line item for new member.
                $orderOption = 1;
                $statement = $connection->prepare("SELECT * FROM orderOptions WHERE id=:id");
                $statement->bindParam(":id", $orderOption);
                $statement->execute();
                $results = $statement->fetch(PDO::FETCH_ASSOC);
                $itemId = $results['id'];
                $itemDescription = "Member Name: " . $registrant->firstName . " " . $registrant->lastName . "\nMember ID: " . $memberId;
                $price = $results['price'];

                array_push($lineItem, array("emailAddress" => $registrant->emailAddress, "userId" => $newUserId, "quantity" => 1, "itemId" => $itemId, "itemName" => $results['description'], "itemDescription" => $itemDescription, "price" => $price));
                
            }

            $lineItems['lineItems'] = $lineItem;

            // Get Billing Business Information
            $statement = $connection->prepare("SELECT * FROM businesses as b, states as s WHERE b.id=:id AND s.stateId=b.state");
            $statement->bindParam(":id", $data->businessId);
            $statement->execute();
            $billingBusiness = $statement->fetch(PDO::FETCH_ASSOC);

            $lineItems['billing'] = array("billingEmailAddress" => $data->emailAddress, "billingBusiness" => $billingBusiness);

            $lineItems = json_encode($lineItems, JSON_PRETTY_PRINT);

            // Remove session data
            $statement = Configuration::openConnection()->prepare("DELETE FROM `userSessions` WHERE `sessionId`=:sessionId");
            $statement->bindParam(":sessionId", $data->sessionId);
            $statement->execute();
            
            Configuration::closeConnection();
            
            return $lineItems;
            
        }
        catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }

    }

    public function getAccountInfo($wsfiaId) {

        try {

            $connection = Configuration::openConnection();

            $statement = $connection->prepare("SELECT `m`.`userId`, `m`.`jobTitle`, `m`.`departments`, `m`.`areas`, `m`.`expirationDate`, `m`.`status`, `m`.`sinceDate`, `m`.`studentId`, `u`.`firstName`, `u`.`lastName` FROM `members` as `m` INNER JOIN `users` as `u` ON `u`.`id`=`m`.`userId` WHERE `m`.`id`=:wsfiaId");
            $statement->bindParam(":wsfiaId", $wsfiaId, PDO::PARAM_STR);
            $statement->execute();

            $result = $statement->fetch(PDO::FETCH_ASSOC);

            $result = json_encode($result, JSON_PRETTY_PRINT);
            
            Configuration::closeConnection();
        }
        catch(PDOException $e) {
            
            $result = '{"result" : ' . $e->getMessage() . '}';
        }
        return $result;
    }

    public function renew($renewData) {

        $data = json_decode(json_encode($renewData), FALSE);

        /**
         * Returns the new User ID in JSON
         */
        try {
            $members = json_decode($data->members);
            //$registrants = json_decode($this->getRegistrants($data->sessionId));
            // $connection is lets you use the same connection for multiple statements.
            $connection = Configuration::openConnection();

            $lineItem = array();

            // Gets new expiration date
            $statement = $connection->prepare("SELECT DATE_FORMAT(CURDATE(), '%Y-%m-%d')");
            $statement->execute();
            $dateCurrent = $statement->fetch(PDO::FETCH_COLUMN);
            if (strtotime($dateCurrent) < strtotime(date('Y-11-1'))) {
                $expirationDate = date('Y-12-31', strtotime($dateCurrent));
            } else {
                $expirationDate = date('Y-12-31', strtotime($dateCurrent . '+1 year'));
            }

            foreach($members as $member) {
                // Update the expiration date
                $statement = $connection->prepare("UPDATE members SET expirationDate=:expirationDate WHERE id=:memberId");
                $statement->bindParam(":expirationDate", $expirationDate);
                $statement->bindParam(":memberId", $member);
                $statement->execute();

                if ($statement->rowCount() > 0) {
                    // Get member information
                    $statement = $connection->prepare("SELECT `members`.`id` AS wsfiaId, `users`.*, `members`.* FROM `members` INNER JOIN `users` ON `users`.`id`=`members`.`userId` WHERE `members`.`id`=:memberId");
                    $statement->bindParam(":memberId", $member);
                    $statement->execute();

                    if ($statement->rowCount() > 0) {
                        $result = $statement->fetch(PDO::FETCH_ASSOC);

                        $userInfo['id'] = $result['id'];
                        $userInfo['wsfiaId'] = $result['wsfiaId'];
                        $userInfo['firstName'] = $result['firstName'];
                        //$userInfo['authenticated'] = password_verify($data->password, $result['password']);

                        // Create an order and line item for new member.
                        $orderOption = 1;
                        $statement = $connection->prepare("SELECT * FROM orderOptions WHERE id=:id");
                        $statement->bindParam(":id", $orderOption);
                        $statement->execute();
                        $results = $statement->fetch(PDO::FETCH_ASSOC);
                        $itemId = $results['id'];
                        $itemDescription = "Member Name: " . $result['firstName'] . " " . $result['lastName'] . "\nMember ID: " . $result['wsfiaId'];
                        $price = $results['price'];

                        array_push($lineItem, array("emailAddress" => $result['emailAddress'], "userId" => $result['userId'], "quantity" => 1, "itemId" => $itemId, "itemName" => $results['description'], "itemDescription" => $itemDescription, "price" => $price));

                    }
                }
            }

            $lineItems['lineItems'] = $lineItem;

            // Get Billing Business Information
            $statement = $connection->prepare("SELECT * FROM businesses as b, states as s WHERE b.id=:id AND s.stateId=b.state");
            $statement->bindParam(":id", $data->businessId);
            $statement->execute();
            $billingBusiness = $statement->fetch(PDO::FETCH_ASSOC);

            $lineItems['billing'] = array("billingEmailAddress" => $data->emailAddress, "billingBusiness" => $billingBusiness);

            $lineItems = json_encode($lineItems, JSON_PRETTY_PRINT);
            
            Configuration::closeConnection();
            
            return $lineItems;
            
        }
        catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }

    }

        
    public function unRegister($userid) {
        /**
         * Returns the number of rows deleted in JSON
         */
       try {
            $statement = Configuration::openConnection()->prepare("DELETE FROM `users` WHERE `id`=:userid");
            $statement->bindParam(":userid", $userid);
            $statement->execute();

            Configuration::closeConnection();

            return json_encode(array("response" => $statement->rowCount()), JSON_PRETTY_PRINT);
        }
        catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }

    }
        
    public function updateRegistration($formData) {
        /**
         * Returns the number of rows updated in JSON
         */
        try {
            // User Information
            /*
            memberId = 'WSFIA-119710'
            */
            $connection = Configuration::openConnection();

            $statement = $connection->prepare("SELECT userId FROM members WHERE id=:id");
            $statement->bindParam(":id", $formData['memberId']);
            $statement->execute();
            $userId = $statement->fetch(PDO::FETCH_COLUMN);

            $member = new Member($userId);

            $statement = $connection->prepare("UPDATE users AS u INNER JOIN members AS m ON u.id = m.userId SET 
                    u.firstName=:firstName, u.lastName=:lastName, u.emailAddress=:emailAddress, 
                    m.jobTitle=:jobTitle, m.departments=:departments, m.areas=:areas, m.studentId=:studentId WHERE m.id=:memberId");
            
            $firstName = (strcasecmp($member->user->getFirstName(), $formData['firstName']) == 0) ? $member->user->getFirstName() : $formData['firstName'];
            $statement->bindParam(":firstName", $firstName);

            $lastName = (strcasecmp($member->user->getLastName(), $formData['lastName']) == 0) ? $member->user->getLastName() : $formData['lastName'];
            $statement->bindParam(":lastName", $lastName);

            $emailAddress = (strcasecmp($member->user->getEmailAddress(), $formData['emailAddress']) == 0) ? $member->user->getEmailAddress() : $formData['emailAddress'];
            $statement->bindParam(":emailAddress", $emailAddress);

            $jobTitle = (strcasecmp($member->getJobTitle(), $formData['jobTitle']) == 0) ? $member->getJobTitle() : $formData['jobTitle'];
            $statement->bindParam(":jobTitle", $jobTitle);

            $departmentIds = array();
            if (isset($formData['departments'])) {
                foreach ($formData['departments'] as $departmentId) {
                    if (!empty($departmentId)) {
                        array_push($departmentIds, (int)$departmentId);
                    }
                }
            }

            $departments = (strcasecmp(json_encode($member->getDepartments()), json_encode($departmentIds)) == 0) ? $member->getDepartments() : $departmentIds;
            $statement->bindParam(":departments", json_encode($departments));

            $areas = array();
            if (isset($formData['areas'])) {
                foreach ($formData['areas'] as $areaId) {
                    if (!empty($areaId)) {
                        array_push($areas, (int)$areaId);
                    }
                }
            }

            $areas = (strcasecmp(json_encode($member->getMemberAreas()), json_encode($areas)) == 0) ? $member->getMemberAreas() : $areas;
            $statement->bindParam(":areas", json_encode($areas));

            $studentId = (strcasecmp($member->getStudentId(), $formData['studentId']) == 0) ? $member->getStudentId() : $formData['studentId'];
            $statement->bindParam(":studentId", $studentId);

            $statement->bindParam(":memberId", $formData['memberId']);

            $statement->execute();

            Configuration::closeConnection();

            return json_encode(array("response" => $statement->rowCount()), JSON_PRETTY_PRINT);

        }
        catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }
        
    public function sendConfirmation($member) {}
        
    public function reportRegistrations() {
        /**
         * Returns information on all members in JSON
         */
        try {
            $statement = Configuration::openConnection()->prepare("SELECT userId FROM members");
            $statement->execute();

            $results = $statement->fetchAll(PDO::FETCH_COLUMN);

            $members = array();
            
            foreach ($results as $index => $id) {
                $member = new Member($id);
                array_push($members, json_decode($member));
            }

            Configuration::closeConnection();
        }
        catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
        $members = json_encode($members, JSON_PRETTY_PRINT);

        return $members;
    }

    public function reportRegistration($id) {
        /**
         * Returns information on a member in JSON
         */
        $members = array();

        $member = new Member($id);

        array_push($members, json_decode($member));

        $members = json_encode($members, JSON_PRETTY_PRINT);
        
        return $members;
    }

    public function createBusiness($business) {
        /**
         * Returns the new Department ID in JSON
         */
        // The department form should be modal on the member info form. So the click on a buttom to bring it up, to add a department.
        $businessId = null;
        try {
            // User Information
            $connection = Configuration::openConnection();

            $statement = $connection->prepare("INSERT INTO businesses (`name`, `station`, `streetAddress`, `city`, `state`, `zipcode`, `phone`, `type`) 
                                                VALUES (:name, :station, :streetAddress, :city, :state, :zipcode, :phone, :type)");
            $statement->bindParam(":name", $business['name']);
            $statement->bindParam(":station", $business['station']);
            $statement->bindParam(":streetAddress", $business['streetAddress']);
            $statement->bindParam(":city", $business['city']);
            $statement->bindParam(":state", $business['state']);
            $statement->bindParam(":zipcode", $business['zipcode']);
            $statement->bindParam(":phone", $business['phone']);
            $statement->bindParam(":type", $business['type']);
            $statement->execute();

            $businessId = $connection->lastInsertId();

            Configuration::closeConnection();

            return json_encode(array("response" => $businessId), JSON_PRETTY_PRINT);

        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
        
        
    }

}

?>