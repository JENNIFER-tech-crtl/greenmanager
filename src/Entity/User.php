<?php  

namespace App\Entity;  
use App\Entity\Task;

use App\Repository\UserRepository;  
use Doctrine\ORM\Mapping as ORM;  
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;  
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;  
use Symfony\Component\Security\Core\User\UserInterface;  
use Symfony\Component\Validator\Constraints as Assert;  
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


#[ORM\Entity(repositoryClass: UserRepository::class)]  
#[UniqueEntity(fields: ['email'], message: 'Il existe déjà un compte avec cet email')]  
class User implements UserInterface, PasswordAuthenticatedUserInterface  
{     
    #[ORM\Id]     
    #[ORM\GeneratedValue]     
    #[ORM\Column]     
    private ?int $id = null;      

    #[ORM\Column(length: 180)]     
    private ?string $email = null;      

    #[ORM\Column]     
    private array $roles = [];      

    #[ORM\Column]     
    private ?string $password = null;      

    #[Assert\NotBlank(groups: ['registration'])]     
    #[Assert\Length(min: 6, groups: ['registration'])]     
    private ?string $plainPassword = null;      

    #[ORM\Column(length: 100)]     
    private ?string $firstName = null;      

    #[ORM\Column(length: 100)]     
    private ?string $lastName = null;      

    #[ORM\Column(length: 255, nullable: true)]     
    private ?string $avatar = null;      

    #[ORM\Column(type: 'boolean')]     
    private bool $isVerified = false;      

    #[ORM\Column(name: "agree_terms", type: "boolean", options: ["default" => false])] 
    private bool $agreeTerms = false;  

    // -------------------- Relation Task --------------------
    #[ORM\OneToMany(mappedBy: 'assignedTo', targetEntity: Task::class)]
    private Collection $tasks;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
        
    $this->notifications = new ArrayCollection();
    }

    public function getTasks(): Collection
    {
        return $this->tasks;
    }
#[ORM\ManyToOne(targetEntity: User::class)]
#[ORM\JoinColumn(nullable: true)]
private ?User $assignedTo = null;

#[ORM\Column(length: 255, nullable: true)]
private ?string $clientEmail = null; // email du client

public function getAssignedTo(): ?User
{
    return $this->assignedTo;
}

public function setAssignedTo(?User $assignedTo): self
{
    $this->assignedTo = $assignedTo;
    return $this;
}

public function getClientEmail(): ?string
{
    return $this->clientEmail;
}

public function setClientEmail(string $clientEmail): self
{
    $this->clientEmail = $clientEmail;
    return $this;
}

    // -------------------- Getters & Setters --------------------      
    public function getId(): ?int { return $this->id; }      

    public function getEmail(): ?string { return $this->email; }  
    public function setEmail(string $email): static { $this->email = $email; return $this; }      

    public function getUserIdentifier(): string { return (string) $this->email; }      

    public function getRoles(): array  
    {  
        $roles = $this->roles;  
        $roles[] = 'ROLE_USER'; // chaque utilisateur a au moins ROLE_USER  
        return array_unique($roles);  
    }      
    public function setRoles(array $roles): static { $this->roles = $roles; return $this; }      
#[ORM\OneToMany(mappedBy: 'user', targetEntity: Notification::class, cascade: ['persist', 'remove'])]
private Collection $notifications;


public function getNotifications(): Collection
{
    return $this->notifications;
}

public function addNotification(Notification $notification): self
{
    if (!$this->notifications->contains($notification)) {
        $this->notifications->add($notification);
        $notification->setUser($this);
    }

    return $this;
}

public function removeNotification(Notification $notification): self
{
    if ($this->notifications->removeElement($notification)) {
        if ($notification->getUser() === $this) {
            $notification->setUser(null);
        }
    }

    return $this;
}

    public function getPassword(): ?string { return $this->password; }  
    public function setPassword(string $password): static { $this->password = $password; return $this; }      

    public function getPlainPassword(): ?string { return $this->plainPassword; }  
    public function setPlainPassword(?string $plainPassword): static { $this->plainPassword = $plainPassword; return $this; }  

    public function eraseCredentials(): void { $this->plainPassword = null; }      

    public function getFirstName(): ?string { return $this->firstName; }  
    public function setFirstName(string $firstName): static { $this->firstName = $firstName; return $this; }      

    public function getLastName(): ?string { return $this->lastName; }  
    public function setLastName(string $lastName): static { $this->lastName = $lastName; return $this; }      

    public function getFullName(): string { return trim(($this->firstName ?? '') . ' ' . ($this->lastName ?? '')); }      

    public function getAvatar(): ?string { return $this->avatar; }  
    public function setAvatar(?string $avatar): static { $this->avatar = $avatar; return $this; }      

    public function isVerified(): bool { return $this->isVerified; }  
    public function setIsVerified(bool $isVerified): static { $this->isVerified = $isVerified; return $this; }      

    public function getAgreeTerms(): bool { return $this->agreeTerms; }  
    public function setAgreeTerms(bool $agreeTerms): self { $this->agreeTerms = $agreeTerms; return $this; }  
}
