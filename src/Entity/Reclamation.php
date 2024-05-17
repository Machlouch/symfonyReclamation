<?php

namespace App\Entity;


use App\Repository\ReclamationRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;


#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['reclamations:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 8,
        max: 255,
        minMessage: "The subject must be at least {{ limit }} characters long.",
        maxMessage: "The subject cannot be longer than {{ limit }} characters."
    )]
    #[Assert\NotBlank(message:"subject not blank")]
    #[Groups(['reclamations:read'])]
    private ?string $Sujet = null;

    #[ORM\Column(length: 500)]
    #[Assert\Length(
        min: 8,
        max: 500,
        minMessage: "The description must be at least {{ limit }} characters long.",
        maxMessage: "The description cannot be longer than {{ limit }} characters."
    )]
    #[Assert\NotBlank(message:"description not blank")]
    #[Groups(['reclamations:read'])]
    private ?string $description = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(['reclamations:read'])]
    private ?\DateTimeInterface $dt = null;

    #[ORM\Column(type: 'boolean')]
    private bool $verified = false;

    #[ORM\OneToMany(targetEntity: RecResponse::class, mappedBy: 'reclamation', cascade: ['persist', 'remove'])]
    private Collection $recResponses;

    public function __construct()
    {
        $this->recResponses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSujet(): ?string
    {
        return $this->Sujet;
    }

    public function setSujet(string $Sujet): self
    {
        $this->Sujet = $Sujet;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDt(): ?\DateTimeInterface
    {
        return $this->dt;
    }

    public function setDt(?\DateTimeInterface $dt): self
    {
        $this->dt = $dt;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->verified;
    }

    public function setVerified(bool $verified): self
    {
        $this->verified = $verified;

        return $this;
    }

    public function getRecResponses(): Collection
    {
        return $this->recResponses;
    }

    public function addRecResponse(RecResponse $recResponse): self
    {
        if (!$this->recResponses->contains($recResponse)) {
            $this->recResponses[] = $recResponse;
            $recResponse->setReclamation($this);
        }

        return $this;
    }

    public function removeRecResponse(RecResponse $recResponse): self
    {
        if ($this->recResponses->removeElement($recResponse)) {
            if ($recResponse->getReclamation() === $this) {
                $recResponse->setReclamation(null);
            }
        }

        return $this;
    }

    #[ORM\PrePersist]
    public function setDtValueOnPrePersist(): void
    {
        if ($this->dt === null) {
            $this->dt = new \DateTime();
        }
    }
}
