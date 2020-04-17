<?php

interface iLineItem {
    /**
     * Add a new Line Item to an order
     */ 
    public function addLineItem($order, $lineItemInfo);
    /**
     * Remove line item from order
     */
    public function removeLineItem($order);
    /**
     * Update line item on order
     */
    public function updateLineItem($order);
    /**
     * Create a list of line items in an order
     */
    public function listLineItems($order);

}

?>