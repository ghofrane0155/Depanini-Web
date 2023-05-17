<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\FeedbacksRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass:FeedbacksRepository::class)]

class Feedbacks
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $idFeedback = null;

    #[ORM\Column(length:255)]
    #[Assert\NotBlank(message: 'Please enter a comment.')]
    #[Assert\Length(max: 255, maxMessage: 'The comment cannot be longer than {{ limit }} characters.')]
    private ?string $commentaire ;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date =null;
    

    #[ORM\Column]
    #[Assert\NotNull(message: 'Please enter a rating.')]
    #[Assert\Range(min: 1, max: 5, minMessage: 'The rating must be between {{ min }} and {{ max }}.', maxMessage: 'The rating must be between {{ min }} and {{ max }}.')]
    private ?float $stars = null;


    
     #[ORM\ManyToOne(targetEntity: Users::class)]
     #[ORM\JoinColumn(name: "id_freelancer", referencedColumnName: "id_user")]
     private ?Users $idFreelancer = null;
    

     #[ORM\ManyToOne(targetEntity: Users::class)]
     #[ORM\JoinColumn(name: "id_client", referencedColumnName: "id_user")]
     private ?Users $idClient = null;

    public function getIdFeedback(): ?int
    {
        return $this->idFeedback;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): self
    {
        $this->commentaire = $commentaire;

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

    public function getStars(): ?float
    {
        return $this->stars;
    }

    public function setStars(float $stars): self
    {
        $this->stars = $stars;

        return $this;
    }

    public function getIdFreelancer(): ?Users
    {
        return $this->idFreelancer;
    }

    public function setIdFreelancer(?Users $idFreelancer): self
    {
        $this->idFreelancer = $idFreelancer;

        return $this;
    }

    public function getIdClient(): ?Users
    {
        return $this->idClient;
    }

    public function setIdClient(?Users $idClient): self
    {
        $this->idClient = $idClient;

        return $this;
    }


}
