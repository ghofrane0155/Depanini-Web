<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Notifications
 *
 * @ORM\Table(name="notifications", indexes={@ORM\Index(name="fk_notifications_feedbacks", columns={"id_feedback"})})
 * @ORM\Entity
 */
class Notifications
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_notification", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idNotification;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_notification", type="date", nullable=false)
     */
    private $dateNotification;

    /**
     * @var string
     *
     * @ORM\Column(name="contenu_notification", type="string", length=255, nullable=false)
     */
    private $contenuNotification;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="marked_as_read", type="date", nullable=true)
     */
    private $markedAsRead;

    /**
     * @var \Feedbacks
     *
     * @ORM\ManyToOne(targetEntity="Feedbacks")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_feedback", referencedColumnName="id_feedback")
     * })
     */
    private $idFeedback;

    public function getIdNotification(): ?int
    {
        return $this->idNotification;
    }

    public function getDateNotification(): ?\DateTimeInterface
    {
        return $this->dateNotification;
    }

    public function setDateNotification(\DateTimeInterface $dateNotification): self
    {
        $this->dateNotification = $dateNotification;

        return $this;
    }

    public function getContenuNotification(): ?string
    {
        return $this->contenuNotification;
    }

    public function setContenuNotification(string $contenuNotification): self
    {
        $this->contenuNotification = $contenuNotification;

        return $this;
    }

    public function getMarkedAsRead(): ?\DateTimeInterface
    {
        return $this->markedAsRead;
    }

    public function setMarkedAsRead(?\DateTimeInterface $markedAsRead): self
    {
        $this->markedAsRead = $markedAsRead;

        return $this;
    }

    public function getIdFeedback(): ?Feedbacks
    {
        return $this->idFeedback;
    }

    public function setIdFeedback(?Feedbacks $idFeedback): self
    {
        $this->idFeedback = $idFeedback;

        return $this;
    }


}
