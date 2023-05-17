<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToMany;
use App\Repository\CommandeRepository;
use Doctrine\ORM\Mapping\InverseJoinColumn;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]

class Commandes
{

    #[JoinTable(name: 'Commandes')]
    #[JoinColumn(name: 'idUser', referencedColumnName: 'idUser')]

    #[InverseJoinColumn(name: 'idProduit', referencedColumnName: 'idProduit')]
    #[ManyToMany(targetEntity: Produits::class)]



    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $idCommande = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateCommande = null;

    #[ORM\Column]
    private ?int $idProduit = null;

    #[ORM\Column]
    private ?int $idUser = null;

    public function getIdCommande(): ?int
    {
        return $this->idCommande;
    }

    public function getDateCommande(): ?\DateTimeInterface
    {
        return $this->dateCommande;
    }

    public function setDateCommande(\DateTimeInterface $dateCommande): self
    {
        $this->dateCommande = $dateCommande;

        return $this;
    }

    public function getIdProduit(): ?int
    {
        return $this->idProduit;
    }

    public function setIdProduit(?int $idProduit): self
    {
        $this->idProduit = $idProduit;

        return $this;
    }

    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    public function setIdUser(?int $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }
}
