<?php

namespace App\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use App\Repository\CategorieRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom ne peut pas être vide")]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La description ne peut pas être vide")]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le service associé ne peut pas être vide")]
    private ?string $service_associe = null;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank(message: "La date de création ne peut pas être vide")]
    private ?\DateTimeInterface $date_de_creation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imagePath = null;

    #[ORM\OneToOne(mappedBy: 'Categoriee', cascade: ['persist', 'remove'])]
    private ?Service $service = null;

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getServiceAssocie(): ?string
    {
        return $this->service_associe;
    }

    public function setServiceAssocie(string $service_associe): static
    {
        $this->service_associe = $service_associe;

        return $this;
    }

    public function getDateDeCreation(): ?\DateTimeInterface
    {
        return $this->date_de_creation;
    }

    public function setDateDeCreation(\DateTimeInterface $date_de_creation): static
    {
        $this->date_de_creation = $date_de_creation;

        return $this;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(string $imagePath): static
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    /**
     * @return Collection<int, Service>
     */

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): static
    {
        // unset the owning side of the relation if necessary
        if ($service === null && $this->service !== null) {
            $this->service->setCategoriee(null);
        }

        // set the owning side of the relation if necessary
        if ($service !== null && $service->getCategoriee() !== $this) {
            $service->setCategoriee($this);
        }

        $this->service = $service;

        return $this;
    }
   

   

    
}