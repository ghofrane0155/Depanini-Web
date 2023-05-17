<?php

namespace App\Entity;

use App\Repository\AdminRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: AdminRepository::class)]

class Admins
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $idAdmin = null;

    #[ORM\Column(length: 100)]
    private ?string $loginAdmin = null;

    #[ORM\Column(length: 100)]
    private ?string $mdpAdmin = null;

    #[ORM\Column(length: 100)]
    private ?string $nomAdmin = null;

    #[ORM\Column(length: 100)]
    private ?string $prenomAdmin = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateNaisAdmin = null;

    #[ORM\Column(length: 100)]
    private ?string $photoAdmin = null;

    #[ORM\Column(length: 10)]
    private ?string $sexeAdmin;

    public function getIdAdmin(): ?int
    {
        return $this->idAdmin;
    }

    public function getLoginAdmin(): ?string
    {
        return $this->loginAdmin;
    }

    public function setLoginAdmin(string $loginAdmin): self
    {
        $this->loginAdmin = $loginAdmin;

        return $this;
    }

    public function getMdpAdmin(): ?string
    {
        return $this->mdpAdmin;
    }

    public function setMdpAdmin(string $mdpAdmin): self
    {
        $this->mdpAdmin = $mdpAdmin;

        return $this;
    }

    public function getNomAdmin(): ?string
    {
        return $this->nomAdmin;
    }

    public function setNomAdmin(string $nomAdmin): self
    {
        $this->nomAdmin = $nomAdmin;

        return $this;
    }

    public function getPrenomAdmin(): ?string
    {
        return $this->prenomAdmin;
    }

    public function setPrenomAdmin(string $prenomAdmin): self
    {
        $this->prenomAdmin = $prenomAdmin;

        return $this;
    }

    public function getDateNaisAdmin(): ?\DateTimeInterface
    {
        return $this->dateNaisAdmin;
    }

    public function setDateNaisAdmin(\DateTimeInterface $dateNaisAdmin): self
    {
        $this->dateNaisAdmin = $dateNaisAdmin;

        return $this;
    }

    public function getPhotoAdmin(): ?string
    {
        return $this->photoAdmin;
    }

    public function setPhotoAdmin(?string $photoAdmin): self
    {
        $this->photoAdmin = $photoAdmin;

        return $this;
    }

    public function getSexeAdmin(): ?string
    {
        return $this->sexeAdmin;
    }

    public function setSexeAdmin(?string $sexeAdmin): self
    {
        $this->sexeAdmin = $sexeAdmin;

        return $this;
    }

    public function __toString()
    {
        return $this->getIdAdmin();
    }

}
