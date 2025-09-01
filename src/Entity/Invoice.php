<?php

namespace App\Entity;

use App\Repository\InvoiceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
#[ORM\Column(length: 255, nullable: true)]
private ?string $client = null;

    #[ORM\Column(type: 'float')]
    private ?float $amount = null;

    #[ORM\Column(length: 50)]
    private ?string $status = 'pending'; // pending, paid, canceled...

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime(); // auto date à la création
    }

    // ---------------- Getters & Setters ---------------- //

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?string
    {
        return $this->client;
    }

    public function setClient(string $client): self
    {
        $this->client = $client;
        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

// src/Entity/Invoice.php

#[ORM\Column(type: 'datetime', nullable: true)]
private ?\DateTimeInterface $dueDate = null;

// Getter
public function getDueDate(): ?\DateTimeInterface
{
    return $this->dueDate;
}

// Setter
public function setDueDate(?\DateTimeInterface $dueDate): self
{
    $this->dueDate = $dueDate;
    return $this;
}

// src/Entity/Invoice.php

#[ORM\Column(type: 'text', nullable: true)]
private ?string $notes = null;

// Getter
public function getNotes(): ?string
{
    return $this->notes;
}

// Setter
public function setNotes(?string $notes): self
{
    $this->notes = $notes;
    return $this;
}
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
    #[ORM\Column(length: 255,nullable: true)]
private ?string $clientEmail = null;

public function getClientEmail(): ?string
{
    return $this->clientEmail;
}

public function setClientEmail(string $clientEmail): self
{
    $this->clientEmail = $clientEmail;
    return $this;
}
#[ORM\ManyToOne(targetEntity: User::class)]
#[ORM\JoinColumn(nullable: true)]
private ?User $assignedTo = null;

public function getAssignedTo(): ?User
{
    return $this->assignedTo;
}

public function setAssignedTo(?User $assignedTo): self
{
    $this->assignedTo = $assignedTo;
    return $this;
}

}
