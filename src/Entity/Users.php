<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("user:read")]
    private ?int $idUser = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank(message:"First Name is required !")]
    #[Groups("user:read")]
    private ?string $nomUser = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank(message:"Last Name is required !")]
    #[Groups("user:read")]
    private ?string $prenomUser = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank(message:"Login is required !")]
    #[Assert\Length(min: 4,max: 10,minMessage: 'Your Username must be at least {{ limit }} characters',
        maxMessage: 'Your Username cannot be longer than {{ limit }} characters',)]
    #[Groups("user:read")]
    private ?string $login = null;

 /**hashed password** */
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Password is required !")]
    #[Assert\Length(min: 8,max: 255,minMessage: 'Your Password must be at least {{ limit }} characters',
        maxMessage: 'Your Password cannot be longer than {{ limit }} characters',)]
    #[Groups("user:read")]
    private ?string $password = null;
    private ?string $plainPassword;
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }
    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password=$password;
        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */ 
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
         $this->plainPassword = null;
    }
/************************************************** */

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message:"Date is required !")]
    #[Assert\GreaterThanOrEqual("01-01-1950",message:"Date can not be accepted")]
    #[Assert\LessThanOrEqual("01-01-2005",message:"You are too young!")]
    #[Groups("user:read")]
    private ?\DateTimeInterface $dateNaisUser = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message:"Email is required !")]
    #[Assert\Email(message:"The email '{{ value }}' is not a valid email ")]
    #[Groups("user:read")]
    private ?string $email = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message:"Adresse is required !")]
    #[Groups("user:read")]
    private ?string $adresse = null;

    #[ORM\Column(length: 8)]
    #[Assert\NotBlank(message:"Phone Number is required !")]
    #[Groups("user:read")]
    private ?string $tel = null;

    #[ORM\Column(length: 10)]
    #[Assert\NotBlank(message:"Sexe is required !")]
    #[Groups("user:read")]
    private ?string $sexe = null;

/********ROLE************************************ */
  #[ORM\Column]
  #[Groups("user:read")]
      private array $roles = [];
  /**
       * A visual identifier that represents this user.
       *
       * @see UserInterface
       */
  
  /********************************************* */
       public function getUserIdentifierID(): int
      {
          return (int) $this->idUser;
      }
      public function getUserIdentifierLOGIN(): string
      {
          return (String) $this->login;
      }
  /********************************************* */
      public function getUserIdentifier(): string
      {
          return (string) $this->email;
      }
          /**
       * @deprecated since Symfony 5.3, use getUserIdentifier instead
       */
      public function getUsername(): string
      {
          return (string) $this->email;
      }
      /**
       * @see UserInterface
       */
      public function getRoles(): array
      {
          $roles = $this->roles;
          // guarantee every user at least has ROLE_USER
          $roles[] = 'ROLE_USER';
  
          return array_unique($roles);
      }
  
      public function setRoles(array $roles): self
      {
          $this->roles = $roles;
  
          return $this;
      }
// /****************************************************************************** */
//     #[ORM\Column(length: 10)]
//   #[Assert\NotBlank(message:"Role is required !")]
     #[Groups("user:read")]
    private ?string $role = null;
///////////////
    #[ORM\Column(length: 100)]
    #[Groups("user:read")]
    private ?string $photoUser;

    private ?string $userBio= null;

    #[ORM\Column]
    private ?bool $isVerified = false;
/*************************************************** */
    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    public function getNomUser(): ?string
    {
        return $this->nomUser;
    }

    public function setNomUser(string $nomUser): self
    {
        $this->nomUser = $nomUser;

        return $this;
    }

    public function getPrenomUser(): ?string
    {
        return $this->prenomUser;
    }

    public function setPrenomUser(string $prenomUser): self
    {
        $this->prenomUser = $prenomUser;

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


    public function getDateNaisUser(): ?\DateTimeInterface
    {
        return $this->dateNaisUser;
    }

    public function setDateNaisUser(\DateTimeInterface $dateNaisUser): self
    {
        $this->dateNaisUser = $dateNaisUser;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getPhotoUser(): ?string
    {
        return $this->photoUser;
    }

    public function setPhotoUser(string $photoUser): self
    {
        $this->photoUser = $photoUser;

        return $this;
    }

    public function __toString()
    {
        return $this->getEmail();
    }

    public function getUserBio(): ?string
    {
        return $this->userBio;
    }

    public function setUserBio(string $userBio): self
    {
        $this->userBio = $userBio;

        return $this;
    }

    public function isIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }
////////////////////////////////////////////////////
#[ORM\Column(length: 180)]
private ?string $reset_token=null;
/**
 * @return mixed
 */
public function getResetToken()
{
    return $this->reset_token;
}

/**
 * @param mixed $reset_token
 */
public function setResetToken($reset_token): void
{
    $this->reset_token = $reset_token;
}

}