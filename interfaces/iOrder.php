<?php

interface iOrder {
    /**
     * Create a new order
     */ 
    public function createOrder($order);
    /**
     * Remove an order
     */
    public function removeOrder($order);
    /**
     * Update an order
     */
    public function updateOrder($order);
    /**
     * Display order with line items
     */
    public function displayOrder($order);

}

?>