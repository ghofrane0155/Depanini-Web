<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass:OffreRepository::class)]

class Offres
{
    #[ORM\Id]
    #[ORM\GeneratedValue]

    #[ORM\Column]
    private $idOffre;

   
    #[ORM\Column]
    #[Assert\NotBlank(message:"Ce champs est obligatoire !")]
    
    private ?float $prixOffre = null;

    #[ORM\Column(length:255)]
    #[Assert\NotBlank(message:"Ce champs est obligatoire !")]
    #[Assert\Length([
        'min' => 5, 
        'minMessage' => 'la description de l offre doit comporter au moins {{ limit }} caractères',
    ])]
    
    private ?string $descriptionOffre= null;

    #[ORM\Column(length:255)]
    #[Assert\NotBlank(message:"Ce champs est obligatoire !")]
    private ?string $localisationOffre= null;

    #[ORM\Column(length:255)]
    #[Assert\NotBlank(message:"Ce champs est obligatoire !")]
    private ?string $nomOffre= null;

    #[ORM\Column(length:255)]
    
    private ?string $imageOffre= null;
  

    #[ORM\Column(length:255)]
    #[Assert\NotBlank(message:"Ce champs est obligatoire !")]
    private ?string $typeOffre= null;


    #[ORM\ManyToOne(targetEntity: Users::class)]
    #[ORM\JoinColumn(name: "id_user", referencedColumnName: "id_user")]
    private ?Users $idUser = null;

    #[ORM\ManyToOne(inversedBy: 'Categories')]
    #[ORM\JoinColumn(nullable:false, name:"id_categorie", referencedColumnName:"id_categorie")]
    private ?Categories $categorie = null;
    public function getIdOffre(): ?int
    {
        return $this->idOffre;
    }

    public function getPrixOffre(): ?float
    {
        return $this->prixOffre;
    }

    public function setPrixOffre(float $prixOffre): self
    {
        $this->prixOffre = $prixOffre;

        return $this;
    }

    public function getDescriptionOffre(): ?string
    {
        return $this->descriptionOffre;
    }

    public function setDescriptionOffre(string $descriptionOffre): self
    {
        $this->descriptionOffre = $descriptionOffre;

        return $this;
    }

    public function getLocalisationOffre(): ?string
    {
        return $this->localisationOffre;
    }

    public function setLocalisationOffre(string $localisationOffre): self
    {
        $this->localisationOffre = $localisationOffre;

        return $this;
    }

    public function getNomOffre(): ?string
    {
        return $this->nomOffre;
    }

    public function setNomOffre(string $nomOffre): self
    {
        $this->nomOffre = $nomOffre;

        return $this;
    }

    public function getImageOffre(): ?string
    {
        return $this->imageOffre;
    }

    public function setImageOffre(string $imageOffre): self
    {
        $this->imageOffre = $imageOffre;

        return $this;
    }

    public function getTypeOffre(): ?string
    {
        return $this->typeOffre;
    }

    public function setTypeOffre(?string $typeOffre): self
    {
        $this->typeOffre = $typeOffre;

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



    public function getCategorie(): ?Categories
    {
        return $this->categorie;
    }

    public function setCategorie(?Categories $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }


}