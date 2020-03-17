<?php
//require_once('../WSFIA.php');
require_once('Order.php');
require_once(__WEBROOT__ . '/configuration/interfaces/iOrder.php');
require_once(__WEBROOT__ . '/configuration/interfaces/iLineItem.php');
/**
 * Handles the processing of order information from registrations.
 */
class ProcessOrder extends Order implements iOrder, iLineItem {

    public $order;
    /**
     * When instantiated it will create a new order.
     */
    public function __construct() {
        //$this->order = new Order($loggedInUser);

        //createOrder($this->order);
    }

    public function createOrder($sessionId) {

        $connection = Configuration::openConnection();

        $statement = $connection->prepare("INSERT INTO orders (`sessionId`) VALUES (:sessionId)");
        $statement->bindParam(":sessionId", $sessionId);
        //$statement->bindParam(":userId", $userId);
        $statement->execute();

        //Configuration::closeConnection();

        return $connection->lastInsertId();
    }
    
    public function removeOrder($order) {}
    
    public function updateOrder($order) {}
    
    public function displayOrder($order) {}

    public function addLineItem($orderId, $lineItemInfo) {
        $connection = Configuration::openConnection();

        $statement = $connection->prepare("INSERT INTO lineItems (`orderId`, `itemId`, `itemDescription`, `price`) VALUES (:orderId, :itemId, :itemDescription, :price)");
        $statement->bindParam(":orderId", $orderId);
        $statement->bindParam(":itemId", $lineItemInfo->itemId);
        $statement->bindParam(":itemDescription", $lineItemInfo->itemDescription);
        $statement->bindParam(":price", $lineItemInfo->price);
        $statement->execute();

        //Configuration::closeConnection();

        return $connection->lastInsertId();
    }
    
    public function removeLineItem($order) {}
    
    public function updateLineItem($order) {}
    
    public function listLineItems($order) {}

}

?>