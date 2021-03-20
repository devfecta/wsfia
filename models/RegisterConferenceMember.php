<?php
require_once('Membership.php');
require_once('./interfaces/iRegistration.php');
/**
 * Handles the conference registration for WSFIA members.
 * Extends Member which extends User.
 */
class RegisterConferenceMember extends Membership implements iRegistration {

    public function __construct() {}

    public function getSession() {
        return $this->sessionData;
    }
    
    public function setSession($sessionData) {}

    public function register($order) {

        $newMember = false;

        if (isset($order['userId'])) {
            $userId = $order['userId'];
        } else {
            $registerMembership = new Membership();
            $userId = json_decode($registerMembership->register($order))->response;

            $newMember = true;
        }

        $member = new Member($userId);

        $processOrder = new ProcessOrder();

        if ($newMember) {

            $processOrder->addLineItem($order);

        } else {}
        
        return $member;
    }
        
    public function unRegister($member) {}
        
    public function updateRegistration($member) {}
        
    public function sendConfirmation($member) {}
        
    public function reportRegistrations() {}

    public function reportRegistration($member) {}
    /**
     * Uses the WSFIA member ID to get add the selected member(s) to the userSessions table, 
     * just like as if they were registering a new account. The only real differenc is the 
     * departments and areas properties.
     */
    public function addConferenceCurrentMembers($sessionData) {

        //return json_encode($sessionData, JSON_PRETTY_PRINT);
        $data = json_decode(json_encode($sessionData), FALSE);

        $memberArray = [];

        $memberIds = explode(',', $data->memberIds);

        foreach($memberIds as $memberId) {

            //return json_encode($memberId, JSON_PRETTY_PRINT);

            try {

                $statement = Configuration::openConnection()->prepare("SELECT u.firstName, u.lastName, u.emailAddress, m.id, m.userId, m.jobTitle, m.departments, m.areas, m.studentId FROM users AS u INNER JOIN members AS m ON u.id=m.userId, statuses AS s WHERE m.id=:id AND s.statusId=m.status");
                $statement->bindParam(":id", $memberId);
                $statement->execute();

                $results = $statement->fetch(PDO::FETCH_ASSOC);

                $departments = json_decode($results['departments'], false);
                $businesses = [];

                foreach($departments as $department) {
                    array_push($businesses, $department->id);
                }

                $results['businesses'] = $businesses;
                unset($results['departments']);


                $areasTemp = json_decode($results['areas'], false);
                $areas = [];

                foreach($areasTemp as $area) {
                    
                    array_push($areas, str_replace("Area ", "", $area));
                }

                $results['areas'] = $areas;

                $result = '';

                try{

                    $connection = Configuration::openConnection();
                    $statement = $connection->prepare("INSERT INTO userSessions (`sessionId`, `registration`) VALUES (:sessionId, :registration)");
                    $statement->bindParam(":sessionId", $data->sessionId);
                    $statement->bindParam(":registration", json_encode($results));

                    $result = json_encode($statement->execute(), JSON_PRETTY_PRINT);
                } catch (Exception $e) {
                    
                    $result = json_encode($e, JSON_PRETTY_PRINT); 

                }

            }
            catch (Exception $e) {
                $result = json_encode($e, JSON_PRETTY_PRINT); 
            }
            finally {
                Configuration::closeConnection();
            }

            

        }

        return $result;

    }

    public function setAttendingDate($attendingData) {

        $data = json_decode(json_encode($attendingData), FALSE);

        try {

            $connection = Configuration::openConnection();

            $statement = $connection->prepare("SELECT * FROM userSessions WHERE sessionId=:id");
            $statement->bindParam(":id", $data->sessionId);
            $statement->execute();

            $registrants = $statement->fetchAll(PDO::FETCH_ASSOC);

            foreach ($registrants as $registrantData) {

                $registrant = json_decode($registrantData['registration'], false);

                if($registrant->emailAddress == $data->emailAddress) {

                    switch ($data->attendingDate) {
                        case 'Monday':
                            $registrant->conference->attending->Monday = $data->attendingChecked;
                            break;
                        case 'Tuesday':
                            $registrant->conference->attending->Tuesday = $data->attendingChecked;
                            break;
                        case 'Wednesday':
                            $registrant->conference->attending->Wednesday = $data->attendingChecked;
                            break;
                        case 'Thursday':
                            $registrant->conference->attending->Thursday = $data->attendingChecked;
                            break;
                        case 'Friday':
                            $registrant->conference->attending->Friday = $data->attendingChecked;
                            break;
                        default:
                            break;
                    }

                    //$registrant->conference->ceu = isset($data->ceu) ? $data->ceu : false;
                    //$registrant->conference->licenseType = isset($data->licenseType) ? $data->licenseType : '';
                    //$registrant->conference->licenseNumber = isset($data->licenseNumber) ? $data->licenseNumber : '';

                    $statement = $connection->prepare("UPDATE `userSessions` SET `registration`=:registration WHERE `id`=:id AND `sessionId`=:sessionId");
                    $statement->bindParam(":id", $registrantData['id']);
                    $statement->bindParam(":sessionId", $data->sessionId);
                    $statement->bindParam(":registration", json_encode($registrant));

                    return json_encode($statement->execute(), JSON_PRETTY_PRINT);
                }
                
            }

        }
        catch (Exception $e) {
            $result = json_encode($e, JSON_PRETTY_PRINT); 
        }
        finally {
            $connection = Configuration::closeConnection();
        }

    }

    public function setCEU($attendingData) {

        $data = json_decode(json_encode($attendingData), FALSE);

        try {

            $connection = Configuration::openConnection();

            $statement = $connection->prepare("SELECT * FROM userSessions WHERE sessionId=:id");
            $statement->bindParam(":id", $data->sessionId);
            $statement->execute();

            $registrants = $statement->fetchAll(PDO::FETCH_ASSOC);

            foreach ($registrants as $registrantData) {

                $registrant = json_decode($registrantData['registration'], false);

                if($registrant->emailAddress == $data->emailAddress) {

                    $registrant->conference->ceu = isset($data->ceu) ? $data->ceu : false;

                    $statement = $connection->prepare("UPDATE `userSessions` SET `registration`=:registration WHERE `id`=:id AND `sessionId`=:sessionId");
                    $statement->bindParam(":id", $registrantData['id']);
                    $statement->bindParam(":sessionId", $data->sessionId);
                    $statement->bindParam(":registration", json_encode($registrant));

                    return json_encode($statement->execute(), JSON_PRETTY_PRINT);
                }
                
            }

        }
        catch (Exception $e) {
            $result = json_encode($e, JSON_PRETTY_PRINT); 
        }
        finally {
            $connection = Configuration::closeConnection();
        }

    }

    public function setLicenseType($attendingData) {

        $data = json_decode(json_encode($attendingData), FALSE);

        try {

            $connection = Configuration::openConnection();

            $statement = $connection->prepare("SELECT * FROM userSessions WHERE sessionId=:id");
            $statement->bindParam(":id", $data->sessionId);
            $statement->execute();

            $registrants = $statement->fetchAll(PDO::FETCH_ASSOC);

            foreach ($registrants as $registrantData) {

                $registrant = json_decode($registrantData['registration'], false);

                if($registrant->emailAddress == $data->emailAddress) {

                    $registrant->conference->licenseType = isset($data->licenseType) ? $data->licenseType : '';

                    $statement = $connection->prepare("UPDATE `userSessions` SET `registration`=:registration WHERE `id`=:id AND `sessionId`=:sessionId");
                    $statement->bindParam(":id", $registrantData['id']);
                    $statement->bindParam(":sessionId", $data->sessionId);
                    $statement->bindParam(":registration", json_encode($registrant));

                    return json_encode($statement->execute(), JSON_PRETTY_PRINT);
                }
                
            }

        }
        catch (Exception $e) {
            $result = json_encode($e, JSON_PRETTY_PRINT); 
        }
        finally {
            $connection = Configuration::closeConnection();
        }

    }

    public function setLicenseNumber($attendingData) {

        $data = json_decode(json_encode($attendingData), FALSE);

        try {

            $connection = Configuration::openConnection();

            $statement = $connection->prepare("SELECT * FROM userSessions WHERE sessionId=:id");
            $statement->bindParam(":id", $data->sessionId);
            $statement->execute();

            $registrants = $statement->fetchAll(PDO::FETCH_ASSOC);

            foreach ($registrants as $registrantData) {

                $registrant = json_decode($registrantData['registration'], false);

                if($registrant->emailAddress == $data->emailAddress) {

                    $registrant->conference->licenseNumber = isset($data->licenseNumber) ? $data->licenseNumber : '';

                    $statement = $connection->prepare("UPDATE `userSessions` SET `registration`=:registration WHERE `id`=:id AND `sessionId`=:sessionId");
                    $statement->bindParam(":id", $registrantData['id']);
                    $statement->bindParam(":sessionId", $data->sessionId);
                    $statement->bindParam(":registration", json_encode($registrant));

                    return json_encode($statement->execute(), JSON_PRETTY_PRINT);
                }
                
            }

        }
        catch (Exception $e) {
            $result = json_encode($e, JSON_PRETTY_PRINT); 
        }
        finally {
            $connection = Configuration::closeConnection();
        }

    }

}

?>