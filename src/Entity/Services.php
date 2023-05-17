<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Services
 *
 * @ORM\Table(name="services")
 * @ORM\Entity
 */
class Services
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_service", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idService;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_service", type="string", length=20, nullable=false)
     */
    private $nomService;

    /**
     * @var string
     *
     * @ORM\Column(name="prix_service", type="string", length=20, nullable=false)
     */
    private $prixService;

    /**
     * @var string
     *
     * @ORM\Column(name="descrption_service", type="string", length=20, nullable=false)
     */
    private $descrptionService;

    public function getIdService(): ?int
    {
        return $this->idService;
    }

    public function getNomService(): ?string
    {
        return $this->nomService;
    }

    public function setNomService(string $nomService): self
    {
        $this->nomService = $nomService;

        return $this;
    }

    public function getPrixService(): ?string
    {
        return $this->prixService;
    }

    public function setPrixService(string $prixService): self
    {
        $this->prixService = $prixService;

        return $this;
    }

    public function getDescrptionService(): ?string
    {
        return $this->descrptionService;
    }

    public function setDescrptionService(string $descrptionService): self
    {
        $this->descrptionService = $descrptionService;

        return $this;
    }


}
