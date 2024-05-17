<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Please enter your email')]
    #[Assert\Email(message: 'Please enter a valid email')]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];  

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
     private ?string $password = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]  
    #[Assert\NotBlank(message: 'Please enter your name')]
    #[Assert\Length(min: 3, max: 255, minMessage: 'Your name should be at least {{ limit }} characters', maxMessage: 'Your name cannot be longer than {{ limit }} characters')]
    private ?string $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'Please enter your last name')]
    #[Assert\Length(min: 3, max: 255, minMessage: 'Your last name should be at least {{ limit }} characters', maxMessage: 'Your last name cannot be longer than {{ limit }} characters')]
    private ?string $LastName = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $UserName = null;

    #[ORM\Column]
    private ?string $role = null;

    #[ORM\Column]
    private ?string $ImagePath = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
   
    #[Assert\Length(min: 6, max: 255, minMessage: 'Your biography should be at least {{ limit }} characters', maxMessage: 'Your biography cannot be longer than {{ limit }} characters')]
    private ?string $bio = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $experience = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $education = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $total_jobs = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $rating = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    
    #[Assert\Length(min: 3, max: 255, minMessage: 'Your domain should be at least {{ limit }} characters', maxMessage: 'Your domain cannot be longer than {{ limit }} characters')]
    private ?string $domaine = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $info = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $location = null;

    #[ORM\Column(type: 'integer',nullable: true)]
    private ?int $nbe = null;

    #[ORM\Column]
    private ?int $isBanned = 0 ;
    #[ORM\Column]
    private ?int $isVerified = 0 ;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->UserName;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->LastName;
    }

    public function setLastName(?string $LastName): self
    {
        $this->LastName = $LastName;

        return $this;
    }

    public function setUserName(?string $UserName): self
    {
        $this->UserName = $UserName;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getImagePath(): ?string
    {
        return $this->ImagePath;
    }

    public function setImagePath(string $ImagePath): self
    {
        $this->ImagePath = $ImagePath;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        $this->bio = $bio;

        return $this;
    }

    public function getExperience(): ?string
    {
        return $this->experience;
    }

    public function setExperience(?string $experience): self
    {
        $this->experience = $experience;

        return $this;
    }

    public function getEducation(): ?string
    {
        return $this->education;
    }

    public function setEducation(?string $education): self
    {
        $this->education = $education;

        return $this;
    }

    public function getTotalJobs(): ?int
    {
        return $this->total_jobs;
    }

    public function setTotalJobs(?int $total_jobs): self
    {
        $this->total_jobs = $total_jobs;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getDomaine(): ?string
    {
        return $this->domaine;
    }

    public function setDomaine(?string $domaine): self
    {
        $this->domaine = $domaine;

        return $this;
    }

    public function getInfo(): ?string
    {
        return $this->info;
    }

    public function setInfo(?string $info): self
    {
        $this->info = $info;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getNbe(): ?int
    {
        return $this->nbe;
    }

    public function setNbe(?int $nbe): self
    {
        $this->nbe = $nbe;

        return $this;
    }

    public function getIsBanned(): ?int
    {
        return $this->isBanned;
    }

    public function setIsBanned(int $isBanned): self
    {
        $this->isBanned = $isBanned;

        return $this;
    }
    public function getIsVerified(): ?int
    {
        return $this->isVerified;
    }

    public function setIsVerified(int $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}
