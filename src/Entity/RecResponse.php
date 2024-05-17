<?php

namespace App\Entity;

use App\Repository\RecResponseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RecResponseRepository::class)]
class RecResponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Reclamation::class, inversedBy: 'recResponses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Reclamation $reclamation = null;

    #[ORM\Column(length: 500)]
    #[Assert\Length(
        min: 8,
        max: 500,
        minMessage: "The response must be at least {{ limit }} characters long.",
        maxMessage: "The response cannot be longer than {{ limit }} characters."
    )]
    #[Assert\NotBlank(message: "response not blank")]
    private ?string $reponse = null;

   /* #[ORM\OneToMany(targetEntity: StarRep::class, mappedBy: 'recResponse')]
    private Collection $starReps;

    public function __construct()
    {
        $this->starReps = new ArrayCollection();
    }*/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReclamation(): ?Reclamation
    {
        return $this->reclamation;
    }

    public function setReclamation(?Reclamation $reclamation): self
    {
        $this->reclamation = $reclamation;

        return $this;
    }

    public function getReponse(): ?string
    {
        return $this->reponse;
    }

    public function setReponse(string $reponse): self
    {
        $this->reponse = $reponse;

        return $this;
    }

    public function getStarReps(): Collection
    {
        return $this->starReps;
    }

    public function addStarRep(StarRep $starRep): self
    {
        if (!$this->starReps->contains($starRep)) {
            $this->starReps->add($starRep);
            $starRep->setRecResponse($this);
        }

        return $this;
    }

    public function removeStarRep(StarRep $starRep): self
    {
        if ($this->starReps->removeElement($starRep)) {
            if ($starRep->getRecResponse() === $this) {
                $starRep->setRecResponse(null);
            }
        }

        return $this;
    }
}
