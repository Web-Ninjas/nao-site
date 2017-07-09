<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var array
     *
     * @ORM\Column(name="roles", type="simple_array")
     */
    private $roles = [];

   

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min=2)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="firstName", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min=2)
     */
    private $firstName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthDate", type="date")
     * @Assert\NotBlank()
     * @Assert\Date()
     * @Assert\LessThan("today")
     */
    private $birthDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registrationDate", type="datetime")
     * @Assert\DateTime()
     */
    private $registrationDate;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * 
     * @Assert\Regex(
     *  pattern="/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
     *  message="Le mot de passe doit avoir au moins 7 caractères et contenir au moins 1 chiffre, une majuscule et une minuscule.")
     *
     * @Assert\Length(max=4096)
     *
     */
    protected $plainPassword;

    /**
     * The below length depends on the "algorithm" you use for encoding
     * the password, but this works well with bcrypt.
     *
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    
    private $ancienMdp;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="demandeNaturaliste", type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $demandeNaturaliste;

    /**
     * @var string
     * @ORM\Column(name="nomEntreprise", type="string", length=255,nullable=true)
     */
    private $nomEntreprise;

    /**
     * @var string
     * @ORM\Column(name="nSiret", type="string", length=255, unique=true, nullable=true)
     */
    private $nSiret;

    /**
     * @return string
     */
    public function getNSiret()
    {
        return $this->nSiret;
    }

    /**
     * @param string $nSiret
     */
    public function setNSiret($nSiret)
    {
        $this->nSiret = $nSiret;
    }

    /**
     * @return mixed
     */
    public function getNomEntreprise()
    {
        return $this->nomEntreprise;
    }

    /**
     * @param mixed $nomEntreprise
     */
    public function setNomEntreprise($nomEntreprise)
    {
        $this->nomEntreprise = $nomEntreprise;
    }

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="demandeContributeur", type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $demandeContributeur;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted", type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $deleted;

    /**
     * @var
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Article", mappedBy="author", fetch="EXTRA_LAZY")
     */
    private $articles;

    
    /**
     * @var
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Observation", mappedBy="author", fetch="EXTRA_LAZY")
     */
    private $observations;


    /**
     * @var string
     *
     * @ORM\Column(name="token_password", type="string", length=255, nullable=true)
     */
    private $tokenRegenerationMotDePasse;

    
    
    
    public function __construct()
    {
        $this->registrationDate = new \DateTime();
        $this->roles = ['ROLE_PARTICULIER'];
    }
    
    /**
     * @return mixed
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * @param mixed $articles
     */
    public function setArticles($articles)
    {
        $this->articles = $articles;
    }

    /**
     * @return mixed
     */
    public function getObservations()
    {
        return $this->observations;
    }

    /**
     * @param mixed $observations
     */
    public function setObservations($observations)
    {
        $this->observations = $observations;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set birthDate
     *
     * @param \DateTime $birthDate
     *
     * @return User
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * Get birthDate
     *
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set registrationDate
     *
     * @param \DateTime $registrationDate
     *
     * @return User
     */
    public function setRegistrationDate($registrationDate)
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    /**
     * Get registrationDate
     *
     * @return \DateTime
     */
    public function getRegistrationDate()
    {
        return $this->registrationDate;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Set demandeNaturaliste
     *
     * @param \DateTime $demandeNaturaliste
     *
     * @return User
     */
    public function setDemandeNaturaliste($demandeNaturaliste)
    {
        $this->demandeNaturaliste = $demandeNaturaliste;

        return $this;
    }

    /**
     * Get demandeNaturaliste
     *
     * @return \DateTime
     */
    public function getDemandeNaturaliste()
    {
        return $this->demandeNaturaliste;
    }

    /**
     * Set demandeContributeur
     *
     * @param \DateTime $demandeContributeur
     *
     * @return User
     */
    public function setDemandeContributeur($demandeContributeur)
    {
        $this->demandeContributeur = $demandeContributeur;

        return $this;
    }

    /**
     * Get demandeContributeur
     *
     * @return \DateTime
     */
    public function getDemandeContributeur()
    {
        return $this->demandeContributeur;
    }

    /**
     * Set deleted
     *
     * @param \DateTime $deleted
     *
     * @return User
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return \DateTime
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set roles
     *
     * @param array $roles
     *
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }


    public function getSalt()
    {
        // The bcrypt algorithm doesn't require a separate salt.
        // You *may* need a real salt if you choose a different encoder.
        return null;
    }

    public function eraseCredentials()
    {
        
    }

    /**
     * @return mixed
     */
    public function getTokenRegenerationMotDePasse()
    {
        return $this->tokenRegenerationMotDePasse;
    }

    /**
     * @param mixed $tokenRegenerationMotDePasse
     */
    public function setTokenRegenerationMotDePasse($tokenRegenerationMotDePasse)
    {
        $this->tokenRegenerationMotDePasse = $tokenRegenerationMotDePasse;
    }
}
