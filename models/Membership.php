<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

require './vendor/autoload.php';
require './vendor/fpdf/fpdf.php';

require_once('Member.php');
require_once('./interfaces/iRegistration.php');

//require_once('./PhpSpreadsheet/IOFactory.php');
//require_once('./PhpSpreadsheet/Spreadsheet.php');
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
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

    public function getRegistrantCount($sessionId) {

        try {
            $connection = Configuration::openConnection();
            $statement = $connection->prepare("SELECT `sessionId` FROM userSessions WHERE `sessionId`=:sessionId");
            $statement->bindParam(":sessionId", $sessionId, PDO::PARAM_STR);
            $statement->execute();
            return $statement->rowCount();
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
        finally {
            $connection = Configuration::closeConnection();
        }

        return $result;

    }

    public function addConferenceRegistrantsREMOVE($sessionData) {

        $data = json_decode(json_encode($sessionData), FALSE);

        foreach($data->memberIds as $memberId) {

            try {
                $connection = Configuration::openConnection();
                $member = new Member($memberId);

                $result = json_encode($member->user, JSON_PRETTY_PRINT);

                /*
                $statement = $connection->prepare("SELECT * FROM users WHERE id=:userId");
                $statement->bindParam(":userId", $memberId);
                $statement->execute();
                $result = $statement->fetch(PDO::FETCH_ASSOC);
                */
            }
            catch (Exception $e) {
                $result = json_encode(array("error" => $e->getMessage()), JSON_PRETTY_PRINT);
            }
            finally {
                $connection = Configuration::closeConnection();
            }

        }

        return $result;

    }
    /**
     * Gets all of the registrants from the userSessions table.
     */
    public function getRegistrants($sessionId) {

        // Array of registrants
        $registrants = array();

        try {
            // Gets all of the registrants based on the session ID.
            $connection = Configuration::openConnection();
            $statement = $connection->prepare("SELECT id, registration FROM userSessions WHERE sessionId=:sessionId");
            $statement->bindParam(":sessionId", $sessionId);
            $statement->execute();
            $results = $statement->fetchAll();

            $statementBusiness = $connection->prepare("SELECT * FROM businesses WHERE id=:businessId");

            foreach($results as $result) {

                $regId = $result['id'];

                $registration = json_decode($result['registration'], false);
                // Changes the area value from just a number to a string with the word Area.
                foreach($registration->areas as $index => $area) {
                    $registration->areas[$index] = "Area $area";
                }
                
                // Iterates over the registrants selected businesses, and selects the business info and adds it to the registration.
                foreach($registration->businesses as $index => $business) {
                    $statementBusiness->bindParam(":businessId", $business);
                    $statementBusiness->execute();
                    $resultsBusiness = $statementBusiness->fetch(PDO::FETCH_ASSOC);
                    // Changes the business value from just a number to an array of business info.
                    $registration->businesses[$index] = $resultsBusiness;
                }

                // Adds the updated registration to the registrants array.
                array_push($registrants, $registration);
                //$registrants = array_filter($registrants);
                
            }

        }
        catch (PDOException $e) { 
            error_log(date('Y-m-d H:i:s') . " " . $e->getMessage() . "\n", 3, "/var/www/html/php-errors.log");
        }
        catch (Exception $e) {
            error_log(date('Y-m-d H:i:s') . " " . $e->getMessage() . "\n", 3, "/var/www/html/php-errors.log");
        }
        finally {
            $connection = Configuration::closeConnection();
        }

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
                $member = new Member($result['id']);
                $userInfo['types'] = $member->user->getUserType();
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

            return json_encode($userInfo, JSON_PRETTY_PRINT);

        }
        catch (PDOException $e) {
            //return "Error: " . $e->getMessage();
            return json_encode(array("error" => $e->getMessage()), JSON_PRETTY_PRINT);
        }
        finally {
            $connection = Configuration::closeConnection();
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
            
        }
        catch (PDOException $e) {
            return $passwordUpdated["updatedPassword"] = $e->getMessage();
        }
        finally {
            $connection = Configuration::closeConnection();
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

        }
        catch (PDOException $e) {
            //return "Error: " . $e->getMessage();
            $result = '{"result" : ' . $e->getMessage() . '}';
        }
        finally {
            Configuration::closeConnection();
        }

        return $result;
    }

    public function removeRegistrant($emailAddress) {
        try {

            $statement = Configuration::openConnection()->prepare("SELECT `id` FROM `userSessions` WHERE `registration` LIKE :emailAddress");
            $statement->bindValue(":emailAddress", '%'.$emailAddress.'%', PDO::PARAM_STR);
            $statement->execute();

            $ids = $statement->fetchAll(PDO::FETCH_COLUMN);

            foreach ($ids as $id) {
                $statement = Configuration::openConnection()->prepare("DELETE FROM `userSessions` WHERE `id`=:id");
                $statement->bindValue(":id", $id, PDO::PARAM_INT);
                //$statement->execute();

                $result = json_encode($statement->execute(), JSON_PRETTY_PRINT);
            }

        }
        catch (PDOException $e) {
            //return "Error: " . $e->getMessage();
            $result = '{"result" : ' . $e->getMessage() . '}';
        }
        finally {
            Configuration::closeConnection();
        }

        return $result;
    }
    /**
     * Registers new members and conference attendees.
     *
     * @param array $sessionData
     * @return json 
     */
    public function register($sessionData) {

        //error_log("Line: " . __LINE__ . " " . date('Y-m-d H:i:s') . " " . json_encode($sessionData, JSON_PRETTY_PRINT) . "\n", 3, "/var/www/html/php-errors.log");

        $data = json_decode(json_encode($sessionData), FALSE);

        $businessId = $data->businessId;
        $emailAddress = $data->emailAddress;

        $lineItems = array();
        
        try {
            // Get registrants from the userSessions table using the session ID.
            $registrants = json_decode($this->getRegistrants($data->sessionId), false);

            // $connection is lets you use the same connection for multiple statements.
            $connection = Configuration::openConnection();

            // Gets the date from the database to use in the member ID.
            $statement = $connection->prepare("SELECT DATE_FORMAT(CURDATE(), '%Y-%m-%d')");
            $statement->execute();
            $dateCurrent = $statement->fetch(PDO::FETCH_COLUMN);

            $memberId = '';
            $lineItem = array();
           
            //$characters = "0123456789abcdefghijklmnopqrstuvwxyz!@#$%&";
            foreach($registrants as $registrant) {
                
                // This variable is for non-member pricing.
                $newMember = false;
                // Checks to see if the registrant is a new member.
                if (!isset($registrant->id)) {

                    $newMember = true;
                    //$password = $characters[mt_rand(0, strlen($Characters))];
                    $password = '123abc';
                    $password = password_hash($password, PASSWORD_BCRYPT);
                    // Inserts new member information into the users table.
                    $statement = $connection->prepare("INSERT INTO users (`type`, `password`, `firstName`, `lastName`, `emailAddress`) VALUES (:type, :password, :firstName, :lastName, :emailAddress)");
                    // Converts array into a string for the database.
                    $memberType = json_encode(array(0=>array("id"=>2, "name"=>"Member")));
                    $registrant->emailAddress = strtolower($registrant->emailAddress);
                    // The default member type is Member.
                    $statement->bindParam(":type", $memberType, PDO::PARAM_STR);
                    $statement->bindParam(":password", $password, PDO::PARAM_STR);
                    $statement->bindParam(":firstName", $registrant->firstName, PDO::PARAM_STR);
                    $statement->bindParam(":lastName", $registrant->lastName, PDO::PARAM_STR);
                    $statement->bindParam(":emailAddress", $registrant->emailAddress, PDO::PARAM_STR);
                    $statement->execute();
                    // Create member ID
                    $newUserId = $connection->lastInsertId();
                    
                    $memberId = 'WSFIA-' . $newUserId . date('ynj', strtotime($dateCurrent));
                    // Converts array into a string for the database.
                    $businesses = json_encode($registrant->businesses);
                    $areas = json_encode($registrant->areas);
                    // Member Information
                    $statement = $connection->prepare("INSERT INTO members (`id`, `userId`, `jobTitle`, `departments`, `areas`, `expirationDate`, `studentId`) VALUES (:id, :userId, :jobTitle, :departments, :areas, :expirationDate, :studentId)");
                    $statement->bindParam(":id", $memberId, PDO::PARAM_STR);
                    $statement->bindParam(":userId", $newUserId, PDO::PARAM_INT);
                    $statement->bindParam(":jobTitle", $registrant->jobTitle, PDO::PARAM_STR);
                    $statement->bindParam(":departments", $businesses, PDO::PARAM_STR);
                    $statement->bindParam(":areas", $areas, PDO::PARAM_STR);
                    // After October the expiration date is increased to the following year.
                    $expirationDate = (strtotime($dateCurrent) < strtotime(date('Y-11-1'))) ? date('Y-12-31', strtotime($dateCurrent)) : date('Y-12-31', strtotime($dateCurrent . '+1 year'));
                    $statement->bindParam(":expirationDate", $expirationDate, PDO::PARAM_STR);
                    $statement->bindParam(":studentId", $registrant->studentId);
                    $statement->execute();
                    // Create an order and line item for new member.
                    // WSFIA Regular Member option ID
                    $orderOption = 1;
                    // Check to see if a student ID has been enter, if so use the student pricing.
                    $studentId = trim($registrant->studentId);
                    if(isset($studentId) && $studentId != '') {
                        // WSFIA Student Member option ID
                        $orderOption = 8;
                    }
                    else {
                        // Checks to see if the registrant selected the lifetime member as a business to get the lifetime member pricing.
                        foreach($registrant->businesses as $business) {
                            if (preg_match('/WSFIA Lifetime Member/i', $business->name)) {
                                // WSFIA Lifetime Member option ID
                                $orderOption = 7;
                                break;
                            }
                        }
                    }
                    // Gets the pricing for the registrant.
                    $statement = $connection->prepare("SELECT * FROM orderOptions WHERE id=:id");
                    $statement->bindParam(":id", $orderOption, PDO::PARAM_INT);
                    $statement->execute();
                    $results = $statement->fetch(PDO::FETCH_ASSOC);
                    // Create a invoice line item for the new membership.
                    $itemDescription = "Member Name: " . $registrant->firstName . " " . $registrant->lastName . "\nMember ID: " . $memberId;    
                    array_push($lineItem, array("emailAddress" => $registrant->emailAddress, "quantity" => 1, "itemId" => $results['id'], "itemName" => $results['description'], "itemDescription" => $itemDescription, "price" => $results['price']));
                }

                // Conference Information
                // Creates a separate line item for conference registrations.
                if (isset($registrant->conference->attending)) {

                    $datesAttending['dates'] = array();

                    //error_log("Line: " . __LINE__ . " " . date('Y-m-d H:i:s') . " " . json_encode($registrant->conference, JSON_PRETTY_PRINT) . "\n", 3, "/var/www/html/php-errors.log");

                    $memberId = isset($registrant->id) ? $registrant->id : $memberId;
                    // Sets the attendind days variables to a boolean.
                    $attendingMonday = (isset($registrant->conference->attending->Monday)) ? filter_var($registrant->conference->attending->Monday, FILTER_VALIDATE_BOOLEAN) : false;
                    $attendingTuesday = (isset($registrant->conference->attending->Tuesday)) ? filter_var($registrant->conference->attending->Tuesday, FILTER_VALIDATE_BOOLEAN) : false;
                    $attendingWednesday = (isset($registrant->conference->attending->Wednesday)) ? filter_var($registrant->conference->attending->Wednesday, FILTER_VALIDATE_BOOLEAN) : false;
                    $attendingThursday = (isset($registrant->conference->attending->Thursday)) ? filter_var($registrant->conference->attending->Thursday, FILTER_VALIDATE_BOOLEAN) : false;
                    $attendingFriday = (isset($registrant->conference->attending->Friday)) ? filter_var($registrant->conference->attending->Friday, FILTER_VALIDATE_BOOLEAN) : false;

                    // Add to the datesAttending array if the registrant is attending a specific day.
                    if ($attendingMonday) array_push($datesAttending['dates'], "Monday");
                    if ($attendingTuesday) array_push($datesAttending['dates'], "Tuesday");
                    if ($attendingWednesday) array_push($datesAttending['dates'], "Wednesday");
                    if ($attendingThursday) array_push($datesAttending['dates'], "Thursday");
                    if ($attendingFriday) array_push($datesAttending['dates'], "Friday");

                    // Adds up the number of days the registrant will be attending the conference.
                    $daysAttending = 0;
                    $daysAttending += $attendingMonday ? 1 : 0;
                    $daysAttending += $attendingTuesday ? 1 : 0;
                    $daysAttending += $attendingWednesday ? 1 : 0;
                    $daysAttending += $attendingThursday ? 1 : 0;
                    $daysAttending += $attendingFriday ? 1 : 0;
                    
                    if ($daysAttending >= 3) {
                        $orderOption = 4;
                    }
                    elseif ($daysAttending == 2) {
                        $orderOption = 3;
                    }
                    elseif ($daysAttending == 1) {
                        $orderOption = 2;
                    }
                    else {
                        $orderOption = 0;
                    }

                    if ($orderOption > 0) {
                        // Gets the pricing for the registrant.
                        $statement = $connection->prepare("SELECT * FROM orderOptions WHERE id=:id");
                        $statement->bindParam(":id", $orderOption, PDO::PARAM_INT);
                        $statement->execute();
                        $results = $statement->fetch(PDO::FETCH_ASSOC);
                        // Adds $40 to the conference pricing for a new member.
                        //$results['price'] = ($newMember) ? $results['price'] + 40 : $results['price'];
                        // Create a invoice line item for the new membership.
                        $itemDescription = "Conference Registration\nMember Name: " . $registrant->firstName . " " . $registrant->lastName . "\nMember ID: " . $memberId;    
                        array_push($lineItem, array("emailAddress" => $registrant->emailAddress, "quantity" => 1, "itemId" => $results['id'], "itemName" => $results['description'], "itemDescription" => $itemDescription, "price" => $results['price']));

                        // Create a invoice line item for the late fee.
                        if (strtotime($dateCurrent) <= strtotime(date('Y-5-1')) && strtotime($dateCurrent) > strtotime(date('Y-9-30'))) {
                            $itemDescription = "Conference Registration Late Fee is for registrations after September 30th";    
                            array_push($lineItem, array("emailAddress" => $registrant->emailAddress, "quantity" => 1, "itemId" => $results['id'], "itemName" => "Registration Late Fee", "itemDescription" => $itemDescription, "price" => 50.00));
                        }

                        $ceu = filter_var($registrant->conference->ceu, FILTER_VALIDATE_BOOLEAN);
                        $ceu = $ceu ? 1 : 0;
                        $licenseType = isset($registrant->conference->licenseType) ? $registrant->conference->licenseType : '';
                        $licenseNumber = isset($registrant->conference->licenseNumber) ? $registrant->conference->licenseNumber : '';

                        
                        // Banquet Registration
                        $banquet = filter_var($registrant->conference->banquet, FILTER_VALIDATE_BOOLEAN);
                        $banquet = $banquet ? 1 : 0;
                        if ($banquet > 0) {
                            // Gets the banquet pricing for the registrant.
                            $statement = $connection->prepare("SELECT * FROM orderOptions WHERE id=6");
                            $statement->execute();
                            $results = $statement->fetch(PDO::FETCH_ASSOC);
                            // If attending 3 or more days the banquet cost is included.
                            $results['price'] = ($daysAttending >= 3) ? 0 : $results['price'];
                            // Create a invoice line item for the banquet registration.
                            $itemDescription = "Banquet Registration\nMember Name: " . $registrant->firstName . " " . $registrant->lastName;    
                            array_push($lineItem, array("emailAddress" => $registrant->emailAddress, "quantity" => 1, "itemId" => $results['id'], "itemName" => $results['description'], "itemDescription" => $itemDescription, "price" => $results['price']));
                        }
                        // Vendor Night Registration
                        $vendorNight = filter_var($registrant->conference->vendorNight, FILTER_VALIDATE_BOOLEAN);
                        $vendorNight = $vendorNight ? 1 : 0;
                        if ($vendorNight > 0) {
                            // Gets the banquet pricing for the registrant.
                            $statement = $connection->prepare("SELECT * FROM orderOptions WHERE id=5");
                            $statement->execute();
                            $results = $statement->fetch(PDO::FETCH_ASSOC);
                            // If attending 3 or more days the vendor night cost is included.
                            $results['price'] = ($daysAttending >= 3) ? 0 : $results['price'];
                            // Create a invoice line item for the vendor night registration.
                            $itemDescription = "Vendor Night Registration\nMember Name: " . $registrant->firstName . " " . $registrant->lastName;    
                            array_push($lineItem, array("emailAddress" => $registrant->emailAddress, "quantity" => 1, "itemId" => $results['id'], "itemName" => $results['description'], "itemDescription" => $itemDescription, "price" => $results['price']));
                        }
                        // Vegetarian Meal
                        $vegetarianMeal = filter_var($registrant->conference->vegetarianMeal, FILTER_VALIDATE_BOOLEAN);
                        $vegetarianMeal = $vegetarianMeal ? 1 : 0;



                        $guests = isset($registrant->conference->guests) ? json_encode($registrant->conference->guests) : null;

                        //error_log("Line: " . __LINE__ . " " . date('Y-m-d H:i:s') . " " . json_encode($guests, JSON_PRETTY_PRINT) . "\n", 3, "/var/www/html/php-errors.log");

                        if (isset($registrant->conference->guests)) {
                            foreach ($registrant->conference->guests as $guest) {
                                if (isset($guest->banquet) && isset($guest->guestName)) {
                                    $banquetGuest = filter_var($guest->banquet, FILTER_VALIDATE_BOOLEAN);
                                    $banquetGuest = $banquetGuest ? 1 : 0;
                                    if ($banquetGuest > 0) {
                                        // Gets the banquet pricing for the registrant.
                                        $statement = $connection->prepare("SELECT * FROM orderOptions WHERE id=6");
                                        $statement->execute();
                                        $results = $statement->fetch(PDO::FETCH_ASSOC);
                                        $itemDescription = $registrant->firstName . " " . $registrant->lastName . "'s Guest: " . $guest->guestName . "'s Banquet Registration";    
                                        array_push($lineItem, array("emailAddress" => $registrant->emailAddress, "quantity" => 1, "itemId" => 0, "itemName" => "Attendee Guest", "itemDescription" => $itemDescription, "price" => $results['price']));
                                    }
                                }

                                if (isset($guest->vendorNight) && isset($guest->guestName)) {
                                    $vendorNightGuest = filter_var($guest->vendorNight, FILTER_VALIDATE_BOOLEAN);
                                    $vendorNightGuest = $vendorNightGuest ? 1 : 0;
                                    if ($vendorNight > 0) {
                                        // Gets the banquet pricing for the registrant.
                                        $statement = $connection->prepare("SELECT * FROM orderOptions WHERE id=5");
                                        $statement->execute();
                                        $results = $statement->fetch(PDO::FETCH_ASSOC);
                                        $itemDescription = $registrant->firstName . " " . $registrant->lastName . "'s Guest: " . $guest->guestName . "'s Vendor Night Registration";    
                                        array_push($lineItem, array("emailAddress" => $registrant->emailAddress, "quantity" => 1, "itemId" => 0, "itemName" => "Attendee Guest", "itemDescription" => $itemDescription, "price" => $results['price']));
                                    }
                                }
                            }
                        }

                        /*
                        $guestName = isset($registrant->conference->guestName) ? $registrant->conference->guestName : '';

                        if (isset($registrant->conference->guestName)) {
                            $guestName = $registrant->conference->guestName;
                            // Create a invoice line item for the banquet registration.
                            $itemDescription = $registrant->firstName . " " . $registrant->lastName . "'s Guest Name: " . $guestName;    
                            array_push($lineItem, array("emailAddress" => $registrant->emailAddress, "quantity" => 1, "itemId" => 0, "itemName" => "Attendee Guest", "itemDescription" => $itemDescription, "price" => 0));
                        }
                        else {
                            $guestName = '';
                        }
                        */



                        // Converts array into a string for the database.
                        $datesAttending = json_encode($datesAttending);
                        // Conference Information
                        $statement = $connection->prepare("INSERT INTO attendees (`memberId`, `datesAttending`, `ceu`, `licenseType`, `licenseNumber`, `banquet`, `vendorNight`, `vegetarianMeal`, `guests`) VALUES (:memberId, :datesAttending, :ceu, :licenseType, :licenseNumber, :banquet, :vendorNight, :vegetarianMeal, :guests)");
                        $statement->bindParam(":memberId", $memberId, PDO::PARAM_STR);
                        $statement->bindParam(":datesAttending", $datesAttending, PDO::PARAM_STR);
                        $statement->bindParam(":ceu", $ceu, PDO::PARAM_INT);
                        $statement->bindParam(":licenseType", $licenseType, PDO::PARAM_STR);
                        $statement->bindParam(":licenseNumber", $licenseNumber, PDO::PARAM_STR);
                        $statement->bindParam(":banquet", $banquet, PDO::PARAM_INT);
                        $statement->bindParam(":vendorNight", $vendorNight, PDO::PARAM_INT);
                        $statement->bindParam(":vegetarianMeal", $vegetarianMeal, PDO::PARAM_INT);
                        $statement->bindParam(":guests", $guests, PDO::PARAM_STR);
                        $statement->execute();
                    }
                }
                // Needs to reset the array completely.
                unset($datesAttending);
                //error_log("Line: " . __LINE__ . " " . date('Y-m-d H:i:s') . " " . json_encode($lineItem, JSON_PRETTY_PRINT) . "\n", 3, "/var/www/html/php-errors.log");
            }

            // Adds all of the new line items to the lineItems array.
            $lineItems['lineItems'] = $lineItem;

            // Get Billing Business Information
            if ($data->businessId > 0) {
                $statement = $connection->prepare("SELECT * FROM businesses as b, states as s WHERE b.id=:id AND s.stateId=b.state");
                $statement->bindParam(":id", $data->businessId, PDO::PARAM_INT);
                $statement->execute();
                $billingBusiness = $statement->fetch(PDO::FETCH_ASSOC);
            }
            else {
                $billingBusiness = array("id" => "0", 
                "name" => $data->otherBillingName, 
                "station" => "", 
                "streetAddress" => $data->otherBillingStreetAddress, 
                "city" => $data->otherBillingCity, 
                "stateAbbreviation" => $data->otherBillingState, 
                "zipcode" => $data->otherBillingZipcode, 
                "phone" => "", 
                "url" => "", 
                "services" => "", 
                "type" => "");
            }

            $billingEmailAddress = ($data->emailAddress != "0") ? $data->emailAddress : $data->otherBillingEmailAddress;

            // Adds billing information to the line items for the invoice.
            $lineItems['billing'] = array("billingEmailAddress" => $billingEmailAddress, "billingBusiness" => $billingBusiness);

            // Removes session data from the database to clean up the userSessions table.
            $statement = Configuration::openConnection()->prepare("DELETE FROM `userSessions` WHERE `sessionId`=:sessionId");
            $statement->bindParam(":sessionId", $data->sessionId);
            $statement->execute();

            //error_log("Line: " . __LINE__ . " " . date('Y-m-d H:i:s') . " " . json_encode($lineItems, JSON_PRETTY_PRINT) . "\n", 3, "/var/www/html/php-errors.log");
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

    public function getAccountInfo($wsfiaId) {

        $result = false;

        try {

            $connection = Configuration::openConnection();

            $statement = $connection->prepare("SELECT `m`.`userId`, `m`.`jobTitle`, `m`.`departments`, `m`.`areas`, `m`.`expirationDate`, `m`.`status`, `m`.`sinceDate`, `m`.`studentId`, `u`.`firstName`, `u`.`lastName` FROM `members` as `m` INNER JOIN `users` as `u` ON `u`.`id`=`m`.`userId` WHERE `m`.`id`=:wsfiaId");
            $statement->bindParam(":wsfiaId", $wsfiaId, PDO::PARAM_STR);
            $statement->execute();

            $result = $statement->fetch(PDO::FETCH_ASSOC);

            $result = json_encode($result, JSON_PRETTY_PRINT);
            
        }
        catch(PDOException $e) {
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

    public function updateAccountInfo($accountData) {
        //return json_encode($accountData, JSON_PRETTY_PRINT);
        $data = json_decode(json_encode($accountData), FALSE);

        $updatedAccount["updatedAccount"] = false;

        try {

            $connection = Configuration::openConnection();

            $statement = $connection->prepare("UPDATE `users` SET `firstName`=:firstName, `lastName`=:lastName WHERE `id`=:userId");
            $statement->bindParam(":userId", $data->userId);
            $statement->bindParam(":firstName", $data->firstName);
            $statement->bindParam(":lastName", $data->lastName);
            $statement->execute();

            $businesses = array();

            $data->businesses = explode(",", $data->businesses);

            foreach($data->businesses as $business) {
                $statement = $connection->prepare("SELECT `b`.* FROM `businesses` AS `b` JOIN `states` AS `s` ON `b`.`state`=`s`.`stateId` WHERE `b`.`id`=:id");
                $statement->bindParam(":id", $business);
                $statement->execute();
                //$results = $statement->fetch(PDO::FETCH_ASSOC);
                array_push($businesses, $statement->fetch(PDO::FETCH_ASSOC));
            }

            //return json_encode($businesses);

            $areas = array();

            $data->areas = explode(",", $data->areas);
            
            foreach($data->areas as $area) {
                array_push($areas, "Area " . $area);
            }

            //return json_encode($areas);

            $statement = $connection->prepare("UPDATE `members` SET `jobTitle`=:jobTitle, `departments`=:departments, `areas`=:areas, `studentId`=:studentId WHERE `userId`=:userId");
            $statement->bindParam(":userId", $data->userId);
            $statement->bindParam(":jobTitle", $data->jobTitle);
            $statement->bindParam(":departments", json_encode($businesses));
            $statement->bindParam(":areas", json_encode($areas));
            $statement->bindParam(":studentId", $data->studentId);
            $statement->execute();

            $updatedAccount["updatedAccount"] = true;

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

        return json_encode($updatedAccount, JSON_PRETTY_PRINT);
    }

    public function renew($renewData) {

        $data = json_decode(json_encode($renewData), FALSE);
        /**
         * Returns the new User ID in JSON
         */
        $lineItems = array();

        try {
            $members = json_decode($data->members);

            //return $members;
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
                        $studentId = trim($result['studentId']);
                        if(isset($studentId) && $studentId != '') {
                            // WSFIA Student Member
                            $orderOption = 8;
                        }
                        else {
                            //WSFIA Lifetime Member
                            $businesses = json_decode($result['departments']);
                            foreach($businesses as $business) {
                                if (preg_match('/WSFIA Lifetime Member/i', $business->name)) {
                                    $orderOption = 7;
                                    break;
                                }
                            }
                        }
                        
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


        error_log(__FILE__ . " Line: " . __LINE__ . " " . date('Y-m-d H:i:s') . $lineItems . "\n", 3, "/var/www/html/php-errors.log");

        return $lineItems;

    }

        
    public function unRegister($userid) {
        /**
         * Returns the number of rows deleted in JSON
         */
       try {

            $connection = Configuration::openConnection();

            $statement = $connection->prepare("DELETE FROM `users` WHERE `id`=:userid");
            $statement->bindParam(":userid", $userid);
            $statement->execute();

        }
        catch (PDOException $e) {
            return "Error: " . $e->getMessage();
            error_log("Line: " . __LINE__ . " " . date('Y-m-d H:i:s') . " " . $e->getMessage() . "\n", 3, "/var/www/html/php-errors.log");
        }
        catch (Exception $e) {
            error_log("Line: " . __LINE__ . " " . date('Y-m-d H:i:s') . " " . $e->getMessage() . "\n", 3, "/var/www/html/php-errors.log");
        }
        finally {
            $connection = Configuration::closeConnection();
        }

        return json_encode(array("response" => $statement->rowCount()), JSON_PRETTY_PRINT);

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

        }
        catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
        finally {
            Configuration::closeConnection();
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
    /**
     * Creates the Excel file of the current members and then saves it directly to Google Drive.
     *
     * @param [type] $service // Google Service Drive object
     * @param [type] $file // Google Service Drive DriveFile object
     * @return void
     */
    public function exportMembersInfo($service, $file) {

        $spreadsheet = new Spreadsheet();

        try {
            $connection = Configuration::openConnection();
            $statement = $connection->prepare("SELECT users.firstName, users.lastName, users.emailAddress, users.type, members.* FROM users INNER JOIN members ON users.id=members.userId");
            $statement->execute();

            $members = $statement->fetchAll(PDO::FETCH_ASSOC);

            $rowCount = 1;
            $columnLetter = 'A';
            $columNames = '';

            //return $members;

            $spreadsheet->setActiveSheetIndex(0);

            for ($c = 0; $c < $statement->columnCount(); $c++) {
                $spreadsheet->getActiveSheet()->setCellValue($columnLetter.($rowCount), $statement->getColumnMeta($c)['name']);
                $columnLetter++;
            }

            foreach($members as $index => $member) {

                $columnLetter = 'A';
                $rowCount++;
                
                for ($c = 0; $c < $statement->columnCount(); $c++) {
                    
                    $value = '';

                    switch($statement->getColumnMeta($c)['name']) {
                        case 'type':
                            // Adds multiple membership types to the one Excel cell for a specific member.
                            $types = json_decode($member[$statement->getColumnMeta($c)['name']]);
                            $lastKey = array_key_last($types);
                            foreach($types as $key => $type) {
                                $value .=  ($lastKey == $key) ? json_decode(json_encode($type))->name : json_decode(json_encode($type))->name . "\n";
                            }
                            /*
                            $types = array();
                            array_push($types, json_decode($member[$statement->getColumnMeta($c)['name']]));
                            foreach($types as $type) {
                                $value .= $type->{key($type)} . "\n";
                            }
                            */
                            break;
                        case 'departments':
                            $originalRowCount = $rowCount;
                            // Adds multiple department/business names to the one Excel cell for a specific member.
                            $departments = json_decode($member[$statement->getColumnMeta($c)['name']]);
                            $lastKey = array_key_last($departments);
                            foreach($departments as $key => $department) {

                                $departmentInfo = json_decode(json_encode($department));

                                $stateId = $departmentInfo->state;
                                $statementDepartment = $connection->prepare("SELECT stateAbbreviation FROM states WHERE stateId=:id");
                                $statementDepartment->bindParam(":id", $stateId, PDO::PARAM_INT);
                                $statementDepartment->execute();
                                $state = $statementDepartment->fetch();

                                // If there is more than one department/business add a new line to the end of the department name.
                                if ($lastKey == $key) {
                                    $value .= $departmentInfo->name . "\n";
                                    $value .= $departmentInfo->streetAddress . "\n";
                                    $value .= $departmentInfo->city . ", ";
                                    $value .= $state['stateAbbreviation'] . " ";
                                    $value .= $departmentInfo->zipcode;
                                }
                                else {
                                    $value .= $departmentInfo->name . "\n";
                                    $value .= $departmentInfo->streetAddress . "\n";
                                    $value .= $departmentInfo->city . ", ";
                                    $value .= $state['stateAbbreviation'] . " ";
                                    $value .= $departmentInfo->zipcode . "\n";
                                }
                                //$value .=  ($lastKey == $key) ? json_decode(json_encode($department))->name : json_decode(json_encode($department))->name . "\n";
                            }
                            break;
                        case 'areas':
                            // Adds multiple areas to the one Excel cell for a specific member.
                            $areas = json_decode($member[$statement->getColumnMeta($c)['name']]);
                            $lastKey = array_key_last($areas);
                            foreach($areas as $key=> $area) {
                                $value .=  ($lastKey == $key) ? $area : $area . "\n";
                            }
                            break;
                        default:
                            // Adds a standard value to the Excel file cell.
                            $value = $member[$statement->getColumnMeta($c)['name']];
                            break;
                    }

                    $spreadsheet->getActiveSheet()->setCellValue($columnLetter.($rowCount), $value);
                    
                    $columnLetter++;
                }
                
            }

            $fileName = 'MemberReport_'. date("Y-m-d", time());

            try{
                // Sets the document type for the file on Google Drive.
                $file->setMimeType("application/vnd.ms-excel");
                // Sets the file name for the file on Google Drive.
                $file->setName($fileName);
                // Sets the folder to which the file should be placed on Google Drive.
                $file->setParents(array("1rxYn-ekD7zoTiXEv2DsgxigVqHJTiZIM"));
                // Creates the file.
                $objWriter = IOFactory::createWriter($spreadsheet, 'Xlsx');
                // Saves the file to a location on the server.
                $objWriter->save("./downloads/".$fileName.".xlsx");
                // Prepares to get the file type information from the server file.
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                // Gets the file type.
                $mime_type = finfo_file($finfo, $objWriter);

                error_log(date('Y-m-d H:i:s') . " Creating file type: " . $mime_type . "\n", 3, "/var/www/html/php-errors.log");

                $result = $service->files->create(
                    $file,
                    array(
                    'data' => file_get_contents("./downloads/".$fileName.".xlsx"),
                    'mimeType' => $mime_type,
                    'uploadType' => 'multipart'
                    )
                );
            }
            catch(Exception $e) {
                error_log(date('Y-m-d H:i:s') . " " . $e->getMessage() . "\n", 3, "/var/www/html/php-errors.log");
            }

            
        }
        catch (PDOException $e) {
            error_log(date('Y-m-d H:i:s') . " " . $e->getMessage() . "\n", 3, "/var/www/html/php-errors.log");
        }
        finally {
            $connection = Configuration::closeConnection();
        }
        /*
        $fileName = 'MemberReport_'. date("Y-m-d", time());
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: attachment;filename=".$fileName.".xlsx");
		header("Content-Transfer-Encoding: binary");
		$objWriter = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $objWriter->save('php://output');
        */
        return $result;
    }

    public function getMembershipCard($memberId) {

        try {
            $connection = Configuration::openConnection();
            $statement = $connection->prepare("SELECT u.firstName, u.lastName, u.emailAddress, m.* FROM users AS u INNER JOIN members AS m ON u.id=m.userId WHERE m.id=:memberId");
            $statement->bindParam(":memberId", $memberId, PDO::PARAM_STR);
            $statement->execute();

            $member = $statement->fetch(PDO::FETCH_ASSOC);

            $pdf = new FPDF('L', 'in', array(3.5, 2));
            $pdf->AddPage();
            $pdf->SetAutoPageBreak(true);
            $pdf->AcceptPageBreak(true);
            $pdf->AliasNbPages();
            
            $pdf->Image('https://wsfia.org/images/WSFIA_Logo.png', .125, 0, .75, .75);

            if (date('Y', strtotime($member['expirationDate'])) % 2 == 0)
            { $pdf->SetFillColor(255, 216, 0); }
            else
            { $pdf->SetFillColor(207, 32, 42); }

            $pdf->Rect(1, 0, 2.5, .75, 'F');

            $pdf->SetFillColor(0);
            $pdf->SetLineWidth(.0375);
            $pdf->Rect(0, 0, 3.5, 2, 'D');



            if (date('Y', strtotime($member['expirationDate'])) % 2 == 0)
            { $pdf->SetTextColor(0); }
            else
            { $pdf->SetTextColor(255); }
            $pdf->SetTextColor(255);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Text(2.05, .225, date('Y', strtotime($member['expirationDate'])));
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Text(1.18, .4, 'Wisconsin State Fire Inspectors');
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Text(1.825, .55, 'Association');
            $pdf->SetFont('Arial', '', 7);
            $pdf->Text(1.925, .675, 'www.wsfia.org');
            
            $pdf->SetTextColor(0);
            $pdf->SetFont('Arial', '', 8);
            $t = 'Dedicated to the prevention of fire through Fire Inspection';
            $s = $pdf->GetStringWidth($t);
            $pdf->Text(.43, .91, $t);
            $t = 'and Public Education';
            $s = $pdf->GetStringWidth($t);
            $pdf->Text(1.2, 1.04, $t);
            
            
            $t = $member['firstName'].' '.$member['lastName'];
            $s = $pdf->GetStringWidth($t);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Text(abs(1.85 - $s), 1.27, $t);
            
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Text(.25, 1.5, 'Membership ID:');
            $pdf->SetFont('Arial', '', 8);
            $pdf->Text(1.15, 1.5, $member['id']);
            /*
            $pdf->SetFont('Arial', 'B', 7);
            $pdf->Text(.25, 1.58, 'Membership Type:');
            $pdf->SetFont('Arial', '', 7);
            $pdf->Text(1.285, 1.58, $User['memberships_Name']);
            */
            /*
            $pdf->SetFont('Arial', 'B', 7);
            $pdf->Text(.25, 1.70, 'Area:');
            $pdf->SetFont('Arial', '', 7);
            $pdf->Text(.55, 1.70, $User['areas_Name'] . ' (' . $User['areas_Description'] . ')');
            */
            $pdf->SetFont('Arial', '', 7);
            $pdf->Text(1, 1.85, 'Membership Expires: '.date('m-d-Y', strtotime($member['expirationDate'])));

        }
        catch (PDOException $e) {
            error_log(date('Y-m-d H:i:s') . " " . $e->getMessage() . "\n", 3, "/var/www/html/php-errors.log");
        }
        catch (Exception $e) {
            error_log(date('Y-m-d H:i:s') . " " . $e->getMessage() . "\n", 3, "/var/www/html/php-errors.log");
        }
        finally {
            $connection = Configuration::closeConnection();
        }
        
        return $pdf->Output('membershipcard.pdf', 'I');
    }

    public function getMembers() {

        $memberList = array();

        try {
            $connection = Configuration::openConnection();
            $statement = $connection->prepare("SELECT users.firstName, users.lastName, users.emailAddress, users.type, members.* FROM users INNER JOIN members ON users.id=members.userId");
            $statement->execute();

            $members = $statement->fetchAll(PDO::FETCH_ASSOC);

            foreach($members as $member) {

                $member['type'] = json_decode($member['type'], false);
                $member['departments'] = json_decode($member['departments'], false);
                $member['areas'] = json_decode($member['areas'], true);

                array_push($memberList, $member);
            }

        }
        catch (PDOException $e) {
            error_log(date('Y-m-d H:i:s') . " " . $e->getMessage() . "\n", 3, "/var/www/html/php-errors.log");
        }
        finally {
            $connection = Configuration::closeConnection();
        }
        
        return json_encode($memberList);
    }
    
}

?>