<?php
/**
 * May be able to remove this in production
 */
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
ob_start();
session_start();
// Loads models and database connection
require_once('Configuration.php');

$requestMethod = $_SERVER['REQUEST_METHOD'];
$registration = null;
$encodedJSON = null;

//$wsfia = new WSFIA();

// Original Configuration::loadModels();
require_once("models/Business.php");
//require_once("./models/Company.php");
//require_once("./models/Confirmation.php");
//require_once("./models/LineItem.php");
require_once("models/Member.php");
require_once("models/Membership.php");
//require_once("./models/Order.php");
require_once("models/ProcessOrder.php");
require_once("models/RegisterConferenceMember.php");
//require_once("./models/RegisterConferenceSpeaker.php");
//require_once("./models/RegisterConferenceVendor.php");
//require_once("./models/School.php");
//require_once("./models/Speaker.php");
//require_once("./models/User.php");
//require_once("./models/Vendor.php");

/*
$connection = Configuration::openConnection();

$id = "1";
$statement = $connection->prepare("SELECT typeName FROM `departmentTypes` WHERE `typeId`=:id");
$statement->bindParam(":id", $id);
$statement->execute();
$typeName = $statement->fetch(PDO::FETCH_COLUMN);

echo "Type:".$typeName;

echo "API <br />";
*/
//echo "Session:".$_COOKIE['PHPSESSID'];

if (isset($_SESSION['userId'])) {
    $userId = $_SESSION['userId'];
} else {
    $userId = null;
}

switch ($requestMethod) {
    case "POST":
    //echo "REQUEST_METHOD Post";
        header('Content-Type: application/json; charset=utf-8');
        //$post = json_decode($_POST, false);
        //echo json_encode(['test' => $_POST]);
        //echo json_encode(array("type" => "API POST", "method" => $requestMethod, "data" => $_POST));
        //exit();
        // Creates
        if (isset($_POST['class'])) {
            switch ($_POST['class']) {
                case "Membership":
                    $Membership = new Membership();
                    switch ($_POST['method']) {
                        case "addMember":
                            // Returns true/false
                            echo $Membership->addMember($_POST);
                            break;
                        case "getRegistrants":
                            // Return JSON of the registrants
                            echo $Membership->getRegistrants($_POST['sessionId']);
                            break;
                        case "register":
                            // Return JSON of the registrants
                            echo $Membership->register($_POST);
                            break;
                        case "login":
                            // Return JSON of the registrants
                            echo $Membership->login($_POST);
                            break;
                        case "resetPassword":
                            // Return JSON of the password boolean
                            echo $Membership->resetPassword($_POST);
                            break;
                        case "renew":
                            // Return JSON of the renewed members
                            //echo json_encode(array("type" => "API POST", "method" => $requestMethod, "data" => $_POST));
                            echo $Membership->renew($_POST);
                            break;
                        default:
                            echo json_encode(array("error" => 'METHOD ERROR: The '.$_POST['method'].' method does not exist.\n'), JSON_PRETTY_PRINT);
                            break;
                    }
                    break;
                case "Business":
                    $Business = new Business(null);
                    switch ($_POST['method']) {
                        case "createBusiness":
                            //echo json_encode($_POST);
                            //exit();
                            echo $Business->createBusiness($_POST);
                            break;
                        default:
                            echo json_encode(array("error" => 'METHOD ERROR: The '.$_POST['method'].' method does not exist.\n'), JSON_PRETTY_PRINT);
                            break;
                    }
                    break;
                default;
                    echo json_encode(array("error" => 'CLASS ERROR: The '.$_POST['class'].' class does not exist.\n'), JSON_PRETTY_PRINT);
                    break;
            }
        }
        break;
    case "GET":
    //echo "REQUEST_METHOD Get";
        // Reads
        if (isset($_GET['class'])) {
            switch ($_GET['class']) {
                case "Membership":
                    $Membership = new Membership();
                    switch ($_GET['method']) {
                        case "getRegistrants":
                            echo $Membership->getRegistrants($_GET['formData']);
                            break;
                        case "getRenewals":
                            $member = new Member(null);
                            echo $member->getMembersByBusiness($_GET['businessId']);
                            break;
                        case "checkEmailAddress":
                            echo $Membership->checkEmailAddress($_GET['searchEmailAddress']);
                            break;
                        default:
                            echo json_encode(array("error" => 'GET METHOD ERROR: The '.$_GET['method'].' method does not exist.\n'), JSON_PRETTY_PRINT);
                            break;
                    }
                    break;
                case 'Member':
                    $member = new Member(null);
                    switch ($_GET['method']) {
                        case "getMembersByBusiness":
                            echo $member->getMembersByBusiness($_GET['businessId']);
                            break;
                        default:
                            echo json_encode(array("error" => 'GET METHOD ERROR: The '.$_GET['method'].' method does not exist.\n'), JSON_PRETTY_PRINT);
                            break;
                    }
                    break;
                case 'Business':
                    $business = new Business(null);
                    switch ($_GET['method']) {
                        case "Business":
                            $business = new Business($_GET['businessId']);
                            echo $business;
                            break;
                        case "getStates":
                            echo $business->getStates();
                            break;
                        case "searchBusinessesByName":
                            echo $business->searchBusinessesByName($_GET['searchBusinesses']);
                            break;
                        default:
                            echo json_encode(array("error" => 'GET METHOD ERROR: The '.$_GET['method'].' method does not exist.\n'), JSON_PRETTY_PRINT);
                            break;
                    }
                    break;
                default;
                    echo json_encode(array("error" => 'GET CLASS ERROR: The '.$_GET['class'].' class does not exist.\n'), JSON_PRETTY_PRINT);
                    break;
            }
        }
        break;
    case "PUT":
    //echo "REQUEST_METHOD Put";
        // Updates
        if (isset($_REQUEST['formType'])) {
            switch ($_REQUEST['formType']) {
                case "UpdateMember":

                    $registration = new Membership();

                    if (isset($_REQUEST['memberId'])) {
                        
                        echo $registration->updateRegistration($_REQUEST);

                    } else {
                        echo json_encode(array("error" => 'PUT ERROR: Member ID not set.\n'), JSON_PRETTY_PRINT);
                    }
                        
                    break;
                default:
                    
                    break;
            }
        } else {
            echo json_encode(array("error" => 'PUT ERROR: Form type not set.\n'), JSON_PRETTY_PRINT);
        }
        break;
    case "DELETE":
    //echo "REQUEST_METHOD Delete";
        // Deletes
        
        if (isset($_GET['formType'])) {
            switch ($_GET['formType']) {
                case "unregisterMembership":

                    $registration = new Membership();

                    if (isset($_GET['id'])) {
                        
                        //echo json_decode($registration->unRegister($_GET['id']));
                        echo $registration->unRegister($_GET['id']);

                        //echo json_encode(array("rowsAffected2" => 'testing'), JSON_PRETTY_PRINT);;
                    } else {
                        echo json_encode(array("error" => 'DELETE ERROR: ID not set.\n'), JSON_PRETTY_PRINT);
                    }
                        
                    break;
                default:
                    
                    break;
            }
        } else {
            echo json_encode(array("error" => 'DELETE ERROR: Form type not set.\n'), JSON_PRETTY_PRINT);
        }
        break;
    default:
    //echo "REQUEST_METHOD Default";
        break;
}

ob_flush();
?>

