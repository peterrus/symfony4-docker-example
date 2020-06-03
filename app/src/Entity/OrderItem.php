<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderItemRepository")
 */
class OrderItem
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Order", inversedBy="items")
     * @ORM\JoinColumn(nullable=false)
     */
    private $parent_order;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getParentOrder(): ?Order
    {
        return $this->parent_order;
    }

    public function setParentOrder(?Order $parent_order): self
    {
        $this->parent_order = $parent_order;

        return $this;
    }
}
