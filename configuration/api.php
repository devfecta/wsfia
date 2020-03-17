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

Configuration::loadModels();
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

        if (isset($_REQUEST['formType'])) {

            switch ($_REQUEST['formType']) {

                case "Membership":

                    $processOrder = new ProcessOrder();
                    $registration = new Membership();

                    //if (!isset($_SESSION['order']['id'])) {
                        
                        $_SESSION['order']['id'] = $processOrder->createOrder(session_id());
                    //}

                    /**
                     * Creates a member but with the status of 0 (unregistered)
                     */

                    $data = json_decode(file_get_contents('php://input'), true);

                    for ($index = 0; $index < sizeof($data); $index++) {
                        foreach ($data[$index] as $key => $value) {
                            $_POST[$key] = $value;
                        }
                        $lineItemInfo = json_decode($registration->register($_POST));
                        $processOrder->addLineItem($_SESSION['order']['id'], $lineItemInfo);
                        $response[] = $lineItemInfo;

                    }

                    //echo json_encode($_POST['firstName']);
                    //echo json_encode($_SESSION['order']['id']);
                    echo json_encode($response);

                    //$_SESSION['order']['member'][] = json_decode($response);

                    //$processOrder->addLineItem($_SESSION['order']['id'], $response);
                    /**
                     * Adds the new member ID and item ID to the session for later use
                     */

                    break;
                case "CreateBusiness":
                    $registration = new Membership();
                    echo $registration->createBusiness($_POST);
                    break;
                case "CreateVendor":
                    break;
                case "CreateSpeaker":
                    break;
                case "CreateConferenceRegistrations":
                    if (isset($_POST['registrantType'])) {

                        switch ($_POST['registrantType']) {
                            case "Member":
                            echo "Conference Member";
                                $registration = new RegisterConferenceMember();
                                echo $registration->register($_POST);
                                break;
                            case "Vendor":
                                break;
                            case "Speaker":
                                break;
                            default:
                                break;
                        }

                    } else {
                        echo json_encode(array("error" => 'POST ERROR: Registrant type not set.\n'), JSON_PRETTY_PRINT);
                    }
                    
                    break;
                default:
                    break;

            }
        } else {

            //echo json_encode(array("error" => 'POST ERROR: Form type not set.\n'), JSON_PRETTY_PRINT);
        
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
                            //echo $_GET['formData'];
                            echo $Membership->getRegistrants($_GET['formData']);
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



        
        if (isset($_GET['formType'])) {
            switch ($_GET['formType']) {

                case "ReadMembers":
                    $registration = new Membership();
                    if (isset($_GET['id']) && $_GET['id'].length > 0) {
                        echo $registration->reportRegistration($_GET['id']);
                    } else {
                        echo $registration->reportRegistrations();
                    }
                    break;
                case "ReadVendors":
                    break;
                case "ReadSpeakers":
                    break;
                case "ReadBusinesses":
                    $business = new Business('');
                    echo $business->getBusinesses();
                    break;
                case "ReadConferenceRegistrations":
                    break;
                default:
                    break;

            }
        } elseif(isset($_GET['view'])) {

            $_SESSION['view'] = $_GET['view'];

            echo file_get_contents('../views/'.$_GET['view'].'.php');

            //Configuration::setView($_GET['view']);

        } elseif(isset($_GET['methodOLD'])) {

            switch ($_GET['method']) {
                case 'searchBusinesses':
                    //$business = new Business(null);
                    //echo $business->searchBusinessesByName($_GET['searchBusinesses']);
                    break;
                case 'getMembers':
                    $member = new Member(null);
                    echo $member->getMembersByBusiness($_GET['businessId']);
                    break;
                default:
                    break;
            }

            

        } else {
           // echo json_encode(array("error" => 'GET ERROR: Form type not set.\n'), JSON_PRETTY_PRINT);
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

