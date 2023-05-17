<?php

namespace App\Entity;

use App\Repository\CategoriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlunk;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoriesRepository::class)]
class Categories
{
    ///
    ///
    ///
    //
    /////
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]

    private ?int $idCategorie = null;

   
    

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Ce champs est obligatoire !")]
    #[Assert\Length([
        'min' => 5, 
        'minMessage' => 'le nom de l offre doit comporter au moins {{ limit }} caractères',
    ])]
    
    private ?string $nomCategorie = null;

    #[ORM\Column(length: 255)]
    private ?string $imageCategorie = null;

    
    /**
     * @return mixed
     */
    public function getOffres()
    {
        return $this->offres;
    }

    /**
     * @param mixed $offres
     */
    public function setOffres($offres): void
    {
        $this->offres = $offres;
    }

    public function __construct()
    {
        
    }

    public function getIdCategorie(): ?int
    {
        return $this->idCategorie;
    }

    public function getNomCategorie(): ?string
    {
        return $this->nomCategorie;
    }

    public function setNomCategorie(string $nomCategorie): self
    {
        $this->nomCategorie = $nomCategorie;

        return $this;
    }

    public function getImageCategorie(): ?string
    {
        return $this->imageCategorie;
    }

    public function setImageCategorie(string $imageCategorie): self
    {
        $this->imageCategorie = $imageCategorie;

        return $this;
    }


    // public function addIdOffre(Offres $idOffre): self
    // {
    //     if (!$this->id_offre->contains($idOffre)) {
    //         $this->id_offre->add($idOffre);
    //         $idOffre->setCategories($this);
    //     }

    //     return $this;
    // }

    // public function removeIdOffre(Offres $idOffre): self
    // {
    //     if ($this->id_offre->removeElement($idOffre)) {
    //         // set the owning side to null (unless already changed)
    //         if ($idOffre->getCategories() === $this) {
    //             $idOffre->setCategories(null);
    //         }
    //     }

    //     return $this;
    // }
}
