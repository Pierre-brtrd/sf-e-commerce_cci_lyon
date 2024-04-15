<?php

namespace App\Entity;

use App\Repository\OrderItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank()]
    #[Assert\PositiveOrZero()]
    private ?int $quantity = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    private ?Produit $product = null;

    #[ORM\ManyToOne(inversedBy: 'items', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $orderRef = null;

    /**
     * Verify if the product is the same as the one in the order item.
     *
     * @param self $item
     * @return boolean
     */
    public function equals(self $item): bool
    {
        return $this->getProduct()->getId() === $item->getProduct()->getId();
    }

    public function getPriceHT(): float
    {
        return $this->getProduct()->getPriceHT() * $this->getQuantity();
    }

    public function getPriceTaxe(): float
    {
        return ($this->getProduct()->getPriceHT() * $this->getProduct()->getTaxe()->getRate()) * $this->getQuantity();
    }

    public function getPriceTTC(): float
    {
        return $this->getPriceHT() + $this->getPriceTaxe();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getProduct(): ?Produit
    {
        return $this->product;
    }

    public function setProduct(?Produit $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getOrderRef(): ?Order
    {
        return $this->orderRef;
    }

    public function setOrderRef(?Order $orderRef): static
    {
        $this->orderRef = $orderRef;

        return $this;
    }
}
