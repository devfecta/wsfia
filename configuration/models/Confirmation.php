<?php
require_once('Order.php');
/**
 * Handles the sending of registration confirmations to WSFIA members.
 */
class Confirmation extends Order {
    /**
     * Send an e-mail confirmation after registration
     */
    public function sendConfirmation($order) {}

}

?>