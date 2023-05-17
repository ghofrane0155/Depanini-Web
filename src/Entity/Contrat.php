<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ContratRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ContratRepository::class)]
class Contrat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $idContart = null;

    #[ORM\ManyToOne(targetEntity: Recrutements::class)]
    #[ORM\JoinColumn(name: "reference", referencedColumnName: "reference")]
    private ?Recrutements $reference = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Ce champs est obligatoire !")]
    private ?string $Etat = 'En attente';

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Ce champs est obligatoire !")]
    private ?string $conditions = null;


    public function getIdContart(): ?int
    {
        return $this->idContart;
    }

    public function getReference(): ?Recrutements
    {
        return $this->reference;
    }

    public function setReference(?Recrutements $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->Etat;
    }

    public function setEtat(string $Etat): self
    {
        $this->Etat = $Etat;

        return $this;
    }

    public function getConditions(): ?string
    {
        return $this->conditions;
    }

    public function setConditions(string $conditions): self
    {
        $this->conditions = $conditions;

        return $this;
    }
    


}