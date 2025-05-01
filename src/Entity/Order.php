<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $customerEmail = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'json')]
    private array $products = [];

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }

    public function getCustomerEmail(): ?string { return $this->customerEmail; }
    public function setCustomerEmail(string $email): self { $this->customerEmail = $email; return $this; }

    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }

    public function getProducts(): array { return $this->products; }
    public function setProducts(array $products): self { $this->products = $products; return $this; }
}
