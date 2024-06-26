<?php

namespace App\Entity;

use App\Entity\Traits\DateTimeTrait;
use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
#[ORM\HasLifecycleCallbacks]
class Order
{
    use DateTimeTrait;

    public const STATUS_CART = 'cart';
    public const STATUS_NEW = 'new';
    public const STATUS_PAID = 'paid';
    public const STATUS_PAYMENT_FAILED = 'payment_failed';
    public const STATUS_CANCELED = 'cancelled';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $number = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Assert\Choice(
        choices: [self::STATUS_CART, self::STATUS_NEW, self::STATUS_PAID, self::STATUS_PAYMENT_FAILED, self::STATUS_CANCELED]
    )]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn()]
    #[Assert\Valid]
    private ?User $user = null;

    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'orderRef', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $items;

    #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'orderRef')]
    private Collection $payments;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->payments = new ArrayCollection();
    }

    public function getPriceHT(): float
    {
        $amount = 0;

        foreach ($this->getItems() as $item) {
            $amount += $item->getPriceHT();
        }

        return $amount;
    }

    public function getPriceTaxe(): float
    {
        $amount = 0;

        foreach ($this->getItems() as $item) {
            $amount += $item->getPriceTaxe();
        }

        return $amount;
    }

    public function getPriceTTC(): float
    {
        $amount = 0;

        foreach ($this->getItems() as $item) {
            $amount += $item->getPriceTTC();
        }

        return $amount;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(OrderItem $item): static
    {
        // On boucle sur les items existants dans la commande
        foreach ($this->items as $existingItem) {
            if ($existingItem->equals($item)) {
                $existingItem->setQuantity(
                    $existingItem->getQuantity() + $item->getQuantity()
                );

                return $this;
            }
        }

        $this->items->add($item);
        $item->setOrderRef($this);

        return $this;
    }

    public function removeItem(OrderItem $item): static
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getOrderRef() === $this) {
                $item->setOrderRef(null);
            }
        }

        return $this;
    }

    public function removeItems(): self
    {
        foreach ($this->getItems() as $item) {
            $this->removeItem($item);
        }

        return $this;
    }

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): static
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setOrderRef($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): static
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getOrderRef() === $this) {
                $payment->setOrderRef(null);
            }
        }

        return $this;
    }
}
