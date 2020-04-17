<?php

class LineItem {

    public $order;

    private $lineItemId;
    private $itemId;
    private $itemDescription;
    private $quantity;
    private $memberDiscount;
    private $price;

    public function __construct($member) {

        //$this->order = new Order($member);
    }

    /**
     * Get the value of lineItemId
     */ 
    public function getLineItemId()
    {
        return $this->lineItemId;
    }

    /**
     * Set the value of lineItemId
     *
     * @return  self
     */ 
    public function setLineItemId($lineItemId)
    {
        $this->lineItemId = $lineItemId;

        return $this;
    }

    /**
     * Get the value of itemId
     */ 
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * Set the value of itemId
     *
     * @return  self
     */ 
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;

        return $this;
    }

    /**
     * Get the value of itemDescription
     */ 
    public function getItemDescription()
    {
        return $this->itemDescription;
    }

    /**
     * Set the value of itemDescription
     *
     * @return  self
     */ 
    public function setItemDescription($itemDescription)
    {
        $this->itemDescription = $itemDescription;

        return $this;
    }

    /**
     * Get the value of quantity
     */ 
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set the value of quantity
     *
     * @return  self
     */ 
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get the value of memberDiscount
     */ 
    public function getMemberDiscount()
    {
        return $this->memberDiscount;
    }

    /**
     * Set the value of memberDiscount
     *
     * @return  self
     */ 
    public function setMemberDiscount($memberDiscount)
    {
        $this->memberDiscount = $memberDiscount;

        return $this;
    }

    /**
     * Get the value of price
     */ 
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set the value of price
     *
     * @return  self
     */ 
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }
}

?>