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

    public function register($order) {}
        
    public function unRegister($member) {}

    public function updateRegistration($member) {}
        
    public function sendConfirmation($member) {}
        
    public function reportRegistrations() {}

    public function reportRegistration($member) {}
    /**
     * Uses the WSFIA member ID to get selected current member information, and adds the member(s) 
     * to the userSessions table, just like as if they were registering a new account.
     * Endpoint: /conference/currentMembers/process
     *
     * @param array $sessionData
     * @return boolean
     */
    public function addConferenceCurrentMembers($currentUsersSessionData) {

        $result = false;
        $data = json_decode(json_encode($currentUsersSessionData), false);
        $memberArray = [];
        $memberIds = explode(',', $data->memberIds);

        foreach($memberIds as $memberId) {

            try {

                $connection = Configuration::openConnection();

                $statement = $connection->prepare("SELECT u.firstName, u.lastName, u.emailAddress, m.id, m.userId, m.jobTitle, m.departments, m.areas, m.studentId FROM users AS u INNER JOIN members AS m ON u.id=m.userId, statuses AS s WHERE m.id=:id AND s.statusId=m.status");
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

                try{
                    $jsonString = json_encode($results);
                    
                    $statement = $connection->prepare("INSERT INTO userSessions (`sessionId`, `registration`) VALUES (:sessionId, :registration)");
                    $statement->bindParam(":sessionId", $data->sessionId, PDO::PARAM_STR);
                    $statement->bindParam(":registration", $jsonString, PDO::PARAM_STR);

                    $result = $statement->execute();
                } catch (Exception $e) {
                    error_log("Line: " . __LINE__ . " " . date('Y-m-d H:i:s') . " " . $e->getMessage() . "\n", 3, "/var/www/html/php-errors.log");
                }

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

        }

        return $result;
    }
    /**
     * Gets all of the current registrations from the session using the session ID.
     *
     * @param array $attendingData
     * @return array registration data
     */
    public function getRegistrations($sessionId) {

        $result = false;

        //error_log("Line: " . __LINE__ . " " . date('Y-m-d H:i:s') . " " . $sessionId . "\n", 3, "/var/www/html/php-errors.log");

        try {

            $connection = Configuration::openConnection();

            $statement = $connection->prepare("SELECT * FROM userSessions WHERE sessionId=:id");
            $statement->bindParam(":id", $sessionId, PDO::PARAM_STR);
            $statement->execute();

            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            
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
    /**
     * Updates the registration session information.
     *
     * @param [type] $registrationId
     * @param [type] $sessionId
     * @param [type] $registration
     * @return boolean
     */
    public function updateSessionRegistration($registrationId, $sessionId, $registration) {

        $result = false;

        try {

            $connection = Configuration::openConnection();

            $statement = $connection->prepare("UPDATE `userSessions` SET `registration`=:registration WHERE `id`=:id AND `sessionId`=:sessionId");
            $statement->bindParam(":id", $registrationId, PDO::PARAM_INT);
            $statement->bindParam(":sessionId", $sessionId, PDO::PARAM_STR);
            $statement->bindParam(":registration", $registration, PDO::PARAM_STR);
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

    /**
     * Sets the conference dates a specific registrant will be attending based on their member ID.
     *
     * @param array $attendingData
     * @return boolean
     */
    public function setAttendingDate($attendingData) {

        $result = false;
        $data = json_decode(json_encode($attendingData), FALSE);

        try {
            
            $registrants = $this->getRegistrations($data->sessionId);

            //error_log("Line: " . __LINE__ . " " . date('Y-m-d H:i:s') . " " . $registrants . "\n", 3, "/var/www/html/php-errors.log");

            foreach ($registrants as $registrantData) {

                $registrant = json_decode($registrantData['registration'], false);

                if($registrant->emailAddress == $data->emailAddress) {

                    $registrant = json_decode($registrantData['registration'], true);

                    switch ($data->attendingDate) {
                        case 'Monday':
                            $registrant['conference']['attending']['Monday'] = $data->attendingChecked;
                            break;
                        case 'Tuesday':
                            $registrant['conference']['attending']['Tuesday'] = $data->attendingChecked;
                            break;
                        case 'Wednesday':
                            $registrant['conference']['attending']['Wednesday'] = $data->attendingChecked;
                            break;
                        case 'Thursday':
                            $registrant['conference']['attending']['Thursday'] = $data->attendingChecked;
                            break;
                        case 'Friday':
                            $registrant['conference']['attending']['Friday'] = $data->attendingChecked;
                            break;
                        default:
                            break;
                    }

                    $registrant = json_encode($registrant);
                    $registrant = json_decode($registrant, false);

                    $jsonString = json_encode($registrant);
                    
                    $result = $this->updateSessionRegistration($registrantData['id'], $data->sessionId, $jsonString);
                }
                
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
    /**
     * Sets the boolean for a specific registrant based on their member ID if they are attending the conference for CEU.
     *
     * @param array $attendingData
     * @return boolean
     */
    public function setCEU($attendingData) {

        $result = false;
        $data = json_decode(json_encode($attendingData), FALSE);

        try {
            
            $registrants = $this->getRegistrations($data->sessionId);

            foreach ($registrants as $registrantData) {

                $registrant = json_decode($registrantData['registration'], false);

                if($registrant->emailAddress == $data->emailAddress) {

                    $registrant = json_decode($registrantData['registration'], true);
                    $registrant['conference']['ceu'] = '';
                    $registrant = json_encode($registrant);
                    $registrant = json_decode($registrant, false);

                    $registrant->conference->ceu = isset($data->ceu) ? $data->ceu : false;

                    $jsonString = json_encode($registrant);
                    
                    $result = $this->updateSessionRegistration($registrantData['id'], $data->sessionId, $jsonString);
                }
                
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
    /**
     * Sets the license type for a specific registrant based on their member ID.
     *
     * @param array $attendingData
     * @return boolean
     */
    public function setLicenseType($attendingData) {

        $result = false;
        $data = json_decode(json_encode($attendingData), FALSE);

        try {
            
            $registrants = $this->getRegistrations($data->sessionId);

            foreach ($registrants as $registrantData) {

                $registrant = json_decode($registrantData['registration'], false);

                if($registrant->emailAddress == $data->emailAddress) {

                    $registrant = json_decode($registrantData['registration'], true);
                    $registrant['conference']['licenseType'] = '';
                    $registrant = json_encode($registrant);
                    $registrant = json_decode($registrant, false);

                    $registrant->conference->licenseType = isset($data->licenseType) ? $data->licenseType : '';

                    $jsonString = json_encode($registrant);
                    
                    $result = $this->updateSessionRegistration($registrantData['id'], $data->sessionId, $jsonString);
                }
                
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
    /**
     * Sets the license number for a specific registrant based on their member ID.
     *
     * @param array $attendingData
     * @return boolean
     */
    public function setLicenseNumber($attendingData) {

        
        $result = false;
        $data = json_decode(json_encode($attendingData), FALSE);
        
        try {

            $registrants = $this->getRegistrations($data->sessionId);

            foreach ($registrants as $registrantData) {

                $registrant = json_decode($registrantData['registration'], false);

                if($registrant->emailAddress == $data->emailAddress) {

                    $registrant = json_decode($registrantData['registration'], true);
                    $registrant['conference']['licenseNumber'] = '';
                    $registrant = json_encode($registrant);
                    $registrant = json_decode($registrant, false);

                    $registrant->conference->licenseNumber = isset($data->licenseNumber) ? $data->licenseNumber : null;
                    
                    $jsonString = json_encode($registrant);

                    $result = $this->updateSessionRegistration($registrantData['id'], $data->sessionId, $jsonString);
                }
                
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
    /**
     * Sets the boolean for a specific registrant based on their member ID if they are attending the conference banquet.
     *
     * @param array $attendingData
     * @return boolean
     */
    public function setBanquet($attendingData) {

        $result = false;
        $data = json_decode(json_encode($attendingData), FALSE);

        try {
            
            $registrants = $this->getRegistrations($data->sessionId);

            foreach ($registrants as $registrantData) {

                $registrant = json_decode($registrantData['registration'], false);

                if($registrant->emailAddress == $data->emailAddress) {

                    $registrant = json_decode($registrantData['registration'], true);
                    $registrant['conference']['banquet'] = '';
                    $registrant = json_encode($registrant);
                    $registrant = json_decode($registrant, false);

                    $registrant->conference->banquet = isset($data->banquet) ? $data->banquet : false;

                    $jsonString = json_encode($registrant);
                    
                    $result = $this->updateSessionRegistration($registrantData['id'], $data->sessionId, $jsonString);
                }
                
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
    /**
     * Sets the boolean for a specific registrant based on their member ID if they are attending the conference vendor night.
     *
     * @param array $attendingData
     * @return boolean
     */
    public function setVendorNight($attendingData) {

        $result = false;
        $data = json_decode(json_encode($attendingData), FALSE);

        try {
            
            $registrants = $this->getRegistrations($data->sessionId);

            foreach ($registrants as $registrantData) {

                $registrant = json_decode($registrantData['registration'], false);

                if($registrant->emailAddress == $data->emailAddress) {

                    $registrant = json_decode($registrantData['registration'], true);
                    $registrant['conference']['vendorNight'] = '';
                    $registrant = json_encode($registrant);
                    $registrant = json_decode($registrant, false);

                    $registrant->conference->vendorNight = isset($data->vendorNight) ? $data->vendorNight : false;

                    $jsonString = json_encode($registrant);
                    
                    $result = $this->updateSessionRegistration($registrantData['id'], $data->sessionId, $jsonString);
                }
                
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

    
    /**
     * Sets the boolean for a specific registrant based on their member ID if they want a vegetarian meal.
     *
     * @param array $attendingData
     * @return boolean
     */
    public function setVegetarianMeal($attendingData) {

        $result = false;
        $data = json_decode(json_encode($attendingData), FALSE);

        try {
            
            $registrants = $this->getRegistrations($data->sessionId);

            foreach ($registrants as $registrantData) {

                $registrant = json_decode($registrantData['registration'], false);

                

                if($registrant->emailAddress == $data->emailAddress) {

                    $registrant = json_decode($registrantData['registration'], true);
                    $registrant['conference']['vegetarianMeal'] = '';
                    $registrant = json_encode($registrant);
                    $registrant = json_decode($registrant, false);

                    //error_log("Line: " . __LINE__ . " " . date('Y-m-d H:i:s') . " " . json_encode($data->vegetarianMeal, JSON_PRETTY_PRINT) . "\n", 3, "/var/www/html/php-errors.log");

                    $registrant->conference->vegetarianMeal = isset($data->vegetarianMeal) ? $data->vegetarianMeal : false;

                    $jsonString = json_encode($registrant);
                    
                    $result = $this->updateSessionRegistration($registrantData['id'], $data->sessionId, $jsonString);
                }
                
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









    /**
     * Sets the boolean for a specific registrant based on their member ID if they are attending the conference banquet.
     *
     * @param array $attendingData
     * @return boolean
     */
    public function setBanquetGuest($attendingData) {

        $result = false;
        $data = json_decode(json_encode($attendingData), FALSE);

        try {
            
            $registrants = $this->getRegistrations($data->sessionId);

            foreach ($registrants as $registrantData) {

                $registrant = json_decode($registrantData['registration'], false);

                if($registrant->emailAddress == $data->emailAddress) {

                    $registrant = json_decode($registrantData['registration'], true);
                    
                    if (sizeof($registrant['conference']['guests']) > 0) {

                        $foundIndex = array_search($data->guestId, array_column($registrant['conference']['guests'], 'id'));

                        if ($foundIndex > -1) {
                            $registrant['conference']['guests'][$foundIndex]['id'] = $data->guestId;
                            $registrant['conference']['guests'][$foundIndex]['banquet'] = isset($data->banquet) ? $data->banquet : null;
                        }
                        else {
                            array_push($registrant['conference']['guests'], ["id" => $data->guestId, "banquet" => isset($data->banquet) ? $data->banquet : null]);
                        }
                    }
                    else {

                        $index = 0;
                        $registrant['conference']['guests'] = array();
                        $registrant['conference']['guests'][0]['id'] = '';
                        $registrant['conference']['guests'][0]['banquet'] = '';
                        $registrant['conference']['guests'][0]['id'] = $data->guestId;
                        $registrant['conference']['guests'][0]['banquet'] = isset($data->banquet) ? $data->banquet : null;
                    }

                    $jsonString = json_encode($registrant);
                    
                    $result = $this->updateSessionRegistration($registrantData['id'], $data->sessionId, $jsonString);
                }
                
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
    /**
     * Sets the boolean for a specific registrant based on their member ID if they are attending the conference vendor night.
     *
     * @param array $attendingData
     * @return boolean
     */
    public function setVendorNightGuest($attendingData) {

        $result = false;
        $data = json_decode(json_encode($attendingData), FALSE);

        try {
            
            $registrants = $this->getRegistrations($data->sessionId);

            foreach ($registrants as $registrantData) {

                $registrant = json_decode($registrantData['registration'], false);

                if($registrant->emailAddress == $data->emailAddress) {

                    $registrant = json_decode($registrantData['registration'], true);

                    if (sizeof($registrant['conference']['guests']) > 0) {

                        $foundIndex = array_search($data->guestId, array_column($registrant['conference']['guests'], 'id'));

                        if ($foundIndex > -1) {
                            $registrant['conference']['guests'][$foundIndex]['id'] = $data->guestId;
                            $registrant['conference']['guests'][$foundIndex]['vendorNight'] = isset($data->vendorNight) ? $data->vendorNight : null;
                        }
                        else {
                            array_push($registrant['conference']['guests'], ["id" => $data->guestId, "vendorNight" => isset($data->vendorNight) ? $data->vendorNight : null]);
                        }
                    }
                    else {

                        $index = 0;
                        $registrant['conference']['guests'] = array();
                        $registrant['conference']['guests'][0]['id'] = '';
                        $registrant['conference']['guests'][0]['vendorNight'] = '';
                        $registrant['conference']['guests'][0]['id'] = $data->guestId;
                        $registrant['conference']['guests'][0]['vendorNight'] = isset($data->vendorNight) ? $data->vendorNight : null;
                    }

                    $jsonString = json_encode($registrant);
                    
                    $result = $this->updateSessionRegistration($registrantData['id'], $data->sessionId, $jsonString);
                }
                
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

    
    /**
     * Sets the boolean for a specific registrant based on their member ID if they want a vegetarian meal.
     *
     * @param array $attendingData
     * @return boolean
     */
    public function setVegetarianMealGuest($attendingData) {

        $result = false;
        $data = json_decode(json_encode($attendingData), FALSE);

        try {
            
            $registrants = $this->getRegistrations($data->sessionId);

            foreach ($registrants as $registrantData) {

                $registrant = json_decode($registrantData['registration'], false);

                if($registrant->emailAddress == $data->emailAddress) {

                    $registrant = json_decode($registrantData['registration'], true);
                
                    if (sizeof($registrant['conference']['guests']) > 0) {

                        $foundIndex = array_search($data->guestId, array_column($registrant['conference']['guests'], 'id'));

                        if ($foundIndex > -1) {
                            $registrant['conference']['guests'][$foundIndex]['id'] = $data->guestId;
                            $registrant['conference']['guests'][$foundIndex]['vegetarianMeal'] = isset($data->vegetarianMeal) ? $data->vegetarianMeal : null;
                        }
                        else {
                            array_push($registrant['conference']['guests'], ["id" => $data->guestId, "vegetarianMeal" => isset($data->vegetarianMeal) ? $data->vegetarianMeal : null]);
                        }
                    }
                    else {

                        $index = 0;
                        $registrant['conference']['guests'] = array();
                        $registrant['conference']['guests'][0]['id'] = '';
                        $registrant['conference']['guests'][0]['vegetarianMeal'] = '';
                        $registrant['conference']['guests'][0]['id'] = $data->guestId;
                        $registrant['conference']['guests'][0]['vegetarianMeal'] = isset($data->vegetarianMeal) ? $data->vegetarianMeal : null;
                    }

                    $jsonString = json_encode($registrant);
                    
                    $result = $this->updateSessionRegistration($registrantData['id'], $data->sessionId, $jsonString);
                }
                
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
    /**
     * Sets guest name for the specific attendee.
     *
     * @param array $attendingData
     * @return boolean
     */
    public function setGuestName($attendingData) {

        
        $result = false;
        $data = json_decode(json_encode($attendingData), FALSE);
        
        try {

            $registrants = $this->getRegistrations($data->sessionId);

            foreach ($registrants as $registrantData) {

                $registrant = json_decode($registrantData['registration'], false);

                if($registrant->emailAddress == $data->emailAddress) {

                    $registrant = json_decode($registrantData['registration'], true);

                    if (sizeof($registrant['conference']['guests']) > 0) {

                        $foundIndex = array_search($data->guestId, array_column($registrant['conference']['guests'], 'id'));

                        if ($foundIndex > -1) {
                            $registrant['conference']['guests'][$foundIndex]['id'] = $data->guestId;
                            $registrant['conference']['guests'][$foundIndex]['guestName'] = isset($data->guestName) ? $data->guestName : null;
                        }
                        else {
                            array_push($registrant['conference']['guests'], ["id" => $data->guestId, "guestName" => isset($data->guestName) ? $data->guestName : null]);
                        }
                    }
                    else {

                        $index = 0;
                        $registrant['conference']['guests'] = array();
                        $registrant['conference']['guests'][0]['id'] = '';
                        $registrant['conference']['guests'][0]['guestName'] = '';
                        $registrant['conference']['guests'][0]['id'] = $data->guestId;
                        $registrant['conference']['guests'][0]['guestName'] = isset($data->guestName) ? $data->guestName : null;
                    }
                    
                    $jsonString = json_encode($registrant);

                    $result = $this->updateSessionRegistration($registrantData['id'], $data->sessionId, $jsonString);
                }
                
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

}

?>