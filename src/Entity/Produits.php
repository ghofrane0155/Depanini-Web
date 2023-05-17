<?php

namespace App\Entity;

use DateTime;
use Assert\NotBlank;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProduitRepository;
use Symfony\component\Validator\constraints as Assert;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]

class Produits
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $idProduit = null;

   
    #[ORM\Column]
    private ?int $idUser = null;

    #[ORM\Column(length: 10)]
    #[Assert\Length(min: 3,max: 10,minMessage: 'Your Password must be at least {{ limit }} characters',
        maxMessage: 'le nom doit etre au maximum {{ limit }} characters',)]
    private ?string $nomProduit = null;

    #[ORM\Column(length: 20)]
    private ?string $categorieProduit = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'Insérez le prix de votre produit.')]
    #[Assert\Positive(message:"Le prix ne peut pas etre négatif")]
    private ?float $prixProduit = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Insérez une description.')]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    public function __construct()
    {
        $this->dateCreation = new DateTime();
    }

    #[ORM\Column]
    private ?int $barecode = null;

    #[ORM\Column(length: 50)]
    private ?string $imageProduit = null;

    public function getIdProduit(): ?int
    {
        return $this->idProduit;
    }

    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    public function setIdUser(int $idUser): self
    {
    $this->idUser = $idUser;

    return $this;
    }

    public function getNomProduit(): ?string
    {
        return $this->nomProduit;
    }

    public function setNomProduit(string $nomProduit): self
    {
        $this->nomProduit = $nomProduit;

        return $this;
    }

    public function getCategorieProduit(): ?string
    {
        return $this->categorieProduit;
    }

    public function setCategorieProduit(string $categorieProduit): self
    {
        $this->categorieProduit = $categorieProduit;

        return $this;
    }

    public function getPrixProduit(): ?float
    {
        return $this->prixProduit;
    }

    public function setPrixProduit(float $prixProduit): self
    {
        $this->prixProduit = $prixProduit;

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

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getBarecode(): ?int
    {
        return $this->barecode;
    }

    public function setBarecode(int $barecode): self
{
    $this->barecode = $barecode;

    return $this;
}

    public function getImageProduit(): ?string
    {
        return $this->imageProduit;
    }

    public function setImageProduit(string $imageProduit): self
    {
        $this->imageProduit = $imageProduit;

        return $this;
    }
}
