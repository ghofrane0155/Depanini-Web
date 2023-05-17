<?php

namespace App\Entity;

use App\Repository\EventsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass:EventsRepository::class)]
// #[ORM\Table(name="Events", indexes={@ORM\Index(columns={})})] 

class Events
{
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("Events")]
    private ?int $idevent=null;

    #[ORM\Column(length:30)]
    #[Groups("Events")]
    #[Assert\NotBlank(message:"This field is required !")]
    private ?string $nomevent=null;

    #[ORM\Column]
    #[Groups("Events")]
    #[Assert\NotBlank(message:"This field is required !")]
    private ?int $nombrelimevent=null;

    #[ORM\Column(length:30)]
    #[Groups("Events")]
    #[Assert\NotBlank(message:"This field is required !")]
    private ?string $lieuevent=null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups("Events")]
    #[Assert\NotBlank(message:"This field is required !")]
    #[Assert\GreaterThanOrEqual('today', message: 'The event date must be greater than or equal to today')]
    private ?\DateTimeInterface $datedebutevent=null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups("Events")]
    #[Assert\NotBlank(message:"This field is required !")]
    #[Assert\GreaterThan(propertyPath: "datedebutevent", message: "The event end date must be greater than the event start date.")]
    private ?\DateTimeInterface $datefinevent=null;

    #[ORM\Column(length:30)]
    #[Groups("Events")]
    #[Assert\NotBlank(message:"This field is required !")]
    private ?string $organisateurevent=null;
    
    #[ORM\Column(length:500)]
    #[Groups("Events")]
    #[Assert\NotBlank(message:"This field is required !")]
    #[Assert\Length([
        'min' => 5, 
        'minMessage' => 'The description of the tender must contain at least {{ limit }} characters',
    ])]
    private ?string $descriptionevent=null;

    #[ORM\Column(length:50)]
    #[Groups("Events")]
    private ?string $imageevent=null;

    #[ORM\Column]
    #[Groups("Events")]
    #[Assert\NotBlank(message:"This field is required !")]
    private ?int $prixevent=null;

    public function getIdevent(): ?int
    {
        return $this->idevent;
    }

    public function getNomevent(): ?string
    {
        return $this->nomevent;
    }
    public function setIdevent(int $idevent): self
    {
        $this->idevent = $idevent;

        return $this;
    }
    public function setNomevent(string $nomevent): self
    {
        $this->nomevent = $nomevent;

        return $this;
    }

    public function getNombrelimevent(): ?int
    {
        return $this->nombrelimevent;
    }

    public function setNombrelimevent(?int $nombrelimevent): self
    {
        $this->nombrelimevent = $nombrelimevent;

        return $this;
    }

    public function getLieuevent(): ?string
    {
        return $this->lieuevent;
    }

    public function setLieuevent(string $lieuevent): self
    {
        $this->lieuevent = $lieuevent;

        return $this;
    }

    public function getDatedebutevent(): ?\DateTimeInterface
    {
        return $this->datedebutevent;
    }

    public function setDatedebutevent(\DateTimeInterface $datedebutevent): self
    {
        $this->datedebutevent = $datedebutevent;

        return $this;
    }

    public function getDatefinevent(): ?\DateTimeInterface
    {
        return $this->datefinevent;
    }

    public function setDatefinevent(\DateTimeInterface $datefinevent): self
    {
        $this->datefinevent = $datefinevent;

        return $this;
    }

    public function getOrganisateurevent(): ?string
    {
        return $this->organisateurevent;
    }

    public function setOrganisateurevent(string $organisateurevent): self
    {
        $this->organisateurevent = $organisateurevent;

        return $this;
    }

    public function getDescriptionevent(): ?string
    {
        return $this->descriptionevent;
    }

    public function setDescriptionevent(string $descriptionevent): self
    {
        $this->descriptionevent = $descriptionevent;

        return $this;
    }

    public function getImageevent(): ?string
    {
        return $this->imageevent;
    }

    public function setImageevent(string $imageevent): self
    {
        $this->imageevent = $imageevent;

        return $this;
    }

    public function getPrixevent(): ?int
    {
        return $this->prixevent;
    }

    public function setPrixevent(int $prixevent): self
    {
        $this->prixevent = $prixevent;

        return $this;
    }
    public function __toString()
    {
        return $this->nomevent;
    }
    

}
