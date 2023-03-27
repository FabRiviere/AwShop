<?php

namespace App\Entity;

use App\Repository\OrdersStateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrdersStateRepository::class)]
class OrdersState
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isPaid = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isShipped = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isDelivery = null;

    #[ORM\OneToMany(mappedBy: 'ordersState', targetEntity: Orders::class)]
    private Collection $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function isIsPaid(): ?bool
    {
        return $this->isPaid;
    }

    public function setIsPaid(?bool $isPaid): self
    {
        $this->isPaid = $isPaid;

        return $this;
    }

    public function isIsShipped(): ?bool
    {
        return $this->isShipped;
    }

    public function setIsShipped(?bool $isShipped): self
    {
        $this->isShipped = $isShipped;

        return $this;
    }

    public function isIsDelivery(): ?bool
    {
        return $this->isDelivery;
    }

    public function setIsDelivery(?bool $isDelivery): self
    {
        $this->isDelivery = $isDelivery;

        return $this;
    }

    /**
     * @return Collection<int, Orders>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Orders $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setOrdersState($this);
        }

        return $this;
    }

    public function removeOrder(Orders $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getOrdersState() === $this) {
                $order->setOrdersState(null);
            }
        }

        return $this;
    }
}
