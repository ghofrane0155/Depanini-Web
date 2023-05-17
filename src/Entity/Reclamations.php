<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass:ReclamationRepository::class)]

class Reclamations
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $idReclamation=null;

    #[ORM\Column(length:100)]
    private ?string $type=null;

    #[Assert\NotNull(message: 'Please enter description.')]
    #[ORM\Column(length:100)]
    private ?string $description=null;

  
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateReclamation=null;

   #[ORM\Column(length:255)]
    private ?string $pieceJointe;

    #[ORM\Column(length:255)]
    private ?string $statut = 'Ouvert';

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateResolution;


    #[ORM\ManyToOne(targetEntity: Admins::class)]
    #[ORM\JoinColumn(name: "id_admin", referencedColumnName: "id_admin")]
    private ?Admins $idAdmin = null;

    #[ORM\ManyToOne(targetEntity: Users::class)]
    #[ORM\JoinColumn(name: "id_user", referencedColumnName: "id_user")]
    private ?Users $idUser = null;



    public function getIdReclamation(): ?int
    {
        return $this->idReclamation;
    }
    public function setIdReclamation(int $idReclamation): self
    {
        $this->idReclamation = $idReclamation;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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

    public function getDateReclamation(): ?\DateTimeInterface
    {
        return $this->dateReclamation;
    }

    public function setDateReclamation(\DateTimeInterface $dateReclamation): self
    {
        $this->dateReclamation = $dateReclamation;

        return $this;
    }

    public function getPieceJointe(): ?string
    {
        return $this->pieceJointe;
    }

    public function setPieceJointe(?string $pieceJointe): self
    {
        $this->pieceJointe = $pieceJointe;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getDateResolution(): ?\DateTimeInterface
    {
        return $this->dateResolution;
    }

    public function setDateResolution(?\DateTimeInterface $dateResolution): self
    {
        $this->dateResolution = $dateResolution;

        return $this;
    }

    public function getIdAdmin(): ?Admins
    {
        return $this->idAdmin;
    }

    public function setIdAdmin(?Admins $idAdmin): self
    {
        $this->idAdmin = $idAdmin;

        return $this;
    }

    public function getIdUser(): ?Users
    {
        return $this->idUser;
    }

    public function setIdUser(?Users $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }


}
