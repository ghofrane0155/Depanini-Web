<?php

namespace App\Entity;

use App\Repository\TicketsRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass:TicketsRepository::class)]
class Tickets
{
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("Tickets")]
    private ?int $idticket=null;

    
    #[ORM\Column]
    #[Groups("Tickets")]
    #[Assert\NotBlank(message:"This field is required !")]
    private ?int $quantite=null;

    #[ORM\Column]
    #[Groups("Tickets")]
    private ?int $prixtotale=null;

    #[ORM\Column(length:100)]
    #[Groups("Tickets")]
    private ?string $login=null;

    #[ORM\ManyToOne(targetEntity: Users::class)]
    #[ORM\JoinColumn(name: "id_user", referencedColumnName: "id_user")]
    #[Groups("Tickets")]
    private ?Users $idUser = null;
   

    #[ORM\ManyToOne(targetEntity: Events::class)]
    #[ORM\JoinColumn(name: "idevent", referencedColumnName: "idevent")] 
    #[Groups("Tickets")]
    private ?Events $idevent = null;

    public function getIdticket(): ?int
    {
        return $this->idticket;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getPrixtotale(): ?int
    {
        return $this->prixtotale;
    }

    public function setPrixtotale(int $prixtotale): self
    {
        $this->prixtotale = $prixtotale;

        return $this;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

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

    public function getIdevent(): ?Events
    {
        return $this->idevent;
    }

    public function setIdevent(?Events $idevent): self
    {
        $this->idevent = $idevent;

        return $this;
    }
    public function getUser(): ?Users
{
    return $this->idUser;
}



}
