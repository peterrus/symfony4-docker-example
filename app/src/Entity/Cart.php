<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * POHO that stores cart contents for a specific user
 * Intended to be serialized into memcached
 * TODO: Better serialize this into json through a mapping inside this object
 */
class Cart
{
    private $owner_id;

    /**
     * associative array, key=product_id, value=amount
     */
    private $items;

    public function __construct($owner_id)
    {
        $this->owner_id = $owner_id;
        $this->items = [];
    }

    public function getOwner(): ?int
    {
        return $this->owner_id;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function addItem(int $product_id)
    {
      if(array_key_exists($product_id, $this->items))
      {
        //product already in cart, increase amount
        $this->items[$product_id]++;
      }
      else {
        // product not yet in cart, add with amount=1
        $this->items[$product_id] = 1;
      }
    }

    public function removeItem(int $product_id)
    {
        unset($this->items[$product_id]);

        return $this;
    }
}
