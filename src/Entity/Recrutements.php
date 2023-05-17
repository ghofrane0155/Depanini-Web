<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\RecrutementsRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RecrutementsRepository::class)]
class Recrutements
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $reference = null;

    
    #[ORM\Column]
    #[Assert\NotBlank(message:"Ce champs est obligatoire !")]
    private ?float $salaire = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank(message:"Ce champs est obligatoire !")]
    #[Assert\Length([
        'min' => 5, 
        'minMessage' => 'la description du poste de travail doit comporter au moins {{ limit }} caractères',
    ])]
    private ?string $description = null ;
 


    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message:"Ce champs est obligatoire !")]
    private ?\DateTimeInterface $date=null;


    #[ORM\Column(length: 150)]
    #[Assert\NotBlank(message:"Ce champs est obligatoire !")]
    private ?string $nom = null ;


    #[ORM\ManyToOne(targetEntity: Users::class)]
    #[ORM\JoinColumn(name: "idclient", referencedColumnName: "id_user")]
    #[Assert\NotBlank(message:"Ce champs est obligatoire !")]
    private ?Users $idclient = null;
    

    public function getReference(): ?int
    {
        return $this->reference;
    }

    public function getSalaire(): ?float
    {
        return $this->salaire;
    }

    public function setSalaire(float $salaire): self
    {
        $this->salaire = $salaire;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getIdclient(): ?Users
    {
        return $this->idclient;
    }

    public function setIdclient(?Users $idclient): self
    {
        $this->idclient = $idclient;

        return $this;
    }
    public function __toString(): string
{
    return $this->reference ?? 'undefined';
}



}