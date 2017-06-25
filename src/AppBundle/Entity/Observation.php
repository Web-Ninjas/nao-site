<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Observation
 *
 * @ORM\Table(name="observation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ObservationRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Observation
{

    const A_VALIDER = "A valider";
    const VALIDEE = "Validée";
    const A_MODIFIER = "A modifier";
    const SIGNALEE = "Signalée";
    const SUPPRIMEE = "Supprimée";
    
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
    * @Assert\NotBlank()
    */
    private $nomOiseau;

    /**
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OiseauTaxref")
    * @ORM\JoinColumn(nullable=true)
    */
    private $oiseau;

    /**
    * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User",inversedBy="observations")
    * @ORM\JoinColumn(nullable=true)
    */
    private $author;
    

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     * @Assert\NotBlank()
     * @Assert\DateTime()
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     * @Assert\NotBlank()
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="commentForNoValidation", type="text", nullable=true)
     */
    private $commentForNoValidation;

    /**
     * @var string
     *
     * @ORM\Column(name="longitude", type="decimal", precision=25, scale=20)
     * @Assert\NotBlank()
     */
    private $longitude;

    /**
     * @var string
     *
     * @ORM\Column(name="latitude", type="decimal", precision=25, scale=20)
     * @Assert\NotBlank()
     */
    private $latitude;

    /**
     * @var string
     *
     * @ORM\Column(name="photo", type="string", length=255, nullable=true)
     * @Assert\File(mimeTypes={ "image/jpeg", "image/png", "image/jpg"})
     * @Assert\Image
     */
    private $photoExtension;

    /**
     * @Assert\File(mimeTypes={ "image/jpeg", "image/png", "image/jpg"})
     */
    private $photoFile;

    private $tempPhotoFilename;

    /**
     * @ORM\Column(name="altPhoto", type="string", length=255, nullable=true)
    */
    private $altPhoto;

    /**
     * @var string
     *
     * @ORM\Column(name="audio", type="string", length=255, nullable=true)
     */
    private $audioExtension;

    private $audioFile;

    private $tempAudioFilename;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="publish", type="datetime", nullable=true)
     */
    private $publish;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted", type="datetime", nullable=true)
     */
    private $deleted;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $validateur;

    public function __construct()
    {
        $this->date = new \DateTime('now');
        $this->status = self::A_VALIDER;
    }

    /**
     * @return mixed
     */
    public function getValidateur()
    {
        return $this->validateur;
    }

    /**
     * @param User $validateur
     */
    public function setValidateur(User $validateur)
    {
        $this->validateur = $validateur;
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
     * @return mixed
     */
    public function getOiseau()
    {
        return $this->oiseau;
    }

    /**
     * @param mixed $oiseau
     */
    public function setOiseau($oiseau)
    {
        $this->oiseau = $oiseau;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }
    
    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Observation
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Observation
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set commentForNoValidation
     *
     * @param string $commentForNoValidation
     *
     * @return Observation
     */
    public function setCommentForNoValidation($commentForNoValidation)
    {
        $this->commentForNoValidation = $commentForNoValidation;

        return $this;
    }

    /**
     * Get commentForNoValidation
     *
     * @return string
     */
    public function getCommentForNoValidation()
    {
        return $this->commentForNoValidation;
    }

    /**
     * Set longitude
     *
     * @param string $longitude
     *
     * @return Observation
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set latitude
     *
     * @param string $latitude
     *
     * @return Observation
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Observation
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set publish
     *
     * @param \DateTime $publish
     *
     * @return Observation
     */
    public function setPublish($publish)
    {
        $this->publish = $publish;

        return $this;
    }

    /**
     * Get publish
     *
     * @return \DateTime
     */
    public function getPublish()
    {
        return $this->publish;
    }

    /**
     * Set deleted
     *
     * @param \DateTime $deleted
     *
     * @return Observation
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

    public function getNomOiseau()
    {
        if ($this->oiseau === null)
        {
            return null;
        }
        return $this->oiseau->getNomValide();
    }

    public function setNomOiseau($nom)
    {
        $this->nomOiseau = $nom;
    }

     /**
      * @ORM\PrePersist()
      * @ORM\PreUpdate()
      */
      public function preUpload()
      {
        // Si jamais il n'y a pas de fichier (champ facultatif), on ne fait rien
        if (null !== $this->photoFile) {
          $this->photoExtension = $this->photoFile->guessExtension();
          $this->altPhoto = $this->photoFile->getClientOriginalName();
        }

        if (null !== $this->audioFile) {
          $this->audioExtension = $this->audioFile->guessExtension();
        }

        return;
      }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        // Si jamais il n'y a pas de fichier (champ facultatif), on ne fait rien
        if (null !== $this->photoFile) 
        {
            // Si on avait un ancien fichier, on le supprime
            if (null !== $this->tempPhotoFilename) 
            {
              $oldFile = $this->getUploadRootDir().'/'.$this->id.'.'.$this->tempPhotoFilename;
                if (file_exists($oldFile)) 
                    unlink($oldFile);
            }

            // On déplace le fichier envoyé dans le répertoire de notre choix
            $this->photoFile->move(
            $this->getUploadRootDir(), // Le répertoire de destination
            $this->id.'.'.$this->photoExtension   // Le nom du fichier à créer, ici « id.extension »
            ); 
        } 

        if (null !== $this->audioFile) 
        {
            // Si on avait un ancien fichier, on le supprime
            if (null !== $this->tempAudioFilename) 
            {
              $oldFile = $this->getUploadRootDir().'/'.$this->id.'.'.$this->tempAudioFilename;
                if (file_exists($oldFile)) 
                    unlink($oldFile);
            }

            // On déplace le fichier envoyé dans le répertoire de notre choix
            $this->audioFile->move(
            $this->getUploadRootDir(), // Le répertoire de destination
            $this->id.'.'.$this->audioExtension   // Le nom du fichier à créer, ici « id.extension »
            ); 
        } 

        return;  
    }

    /**
    * @ORM\PreRemove()
    */
    public function preRemoveUpload()
    {
     // On sauvegarde temporairement le nom du fichier, car il dépend de l'id
     $this->tempPhotoFilename = $this->getUploadRootDir().'/'.$this->id.'.'.$this->photoExtension;
     $this->tempAudioFilename = $this->getUploadRootDir().'/'.$this->id.'.'.$this->audioExtension;
    }

    /**
     * @ORM\PostRemove()
     */
     public function removeUpload()
     {
        // En PostRemove, on n'a pas accès à l'id, on utilise notre nom sauvegardé
        if (file_exists($this->tempPhotoFilename)) 
        {
            // On supprime le fichier
            unlink($this->tempPhotoFilename);
        }

        if (file_exists($this->tempAudioFilename)) 
        {
            // On supprime le fichier
            unlink($this->tempAudioFilename);
        }
    }

    public function getUploadDir()
    {
        // On retourne le chemin relatif vers l'image pour un navigateur (relatif au répertoire /web donc)
        return 'uploads/observations';
    }

    protected function getUploadRootDir()
    {
        // On retourne le chemin relatif vers l'image pour notre code PHP
        return __DIR__.'/../../../web/'.$this->getUploadDir();
    }

    public function getPhotoWebPath()
    {
        return $this->getUploadDir().'/'.$this->getId().'.'.$this->getPhotoExtension();
    }

    public function getAudioWebPath()
    {
        return $this->getUploadDir().'/'.$this->getId().'.'.$this->getAudioExtension();
    }

    /**
     * Set photoExtension
     *
     * @param string $photoExtension
     *
     * @return Observation
     */
    public function setPhotoExtension($photoExtension)
    {
        $this->photoExtension = $photoExtension;

        return $this;
    }

    /**
     * Get photoExtension
     *
     * @return string
     */
    public function getPhotoExtension()
    {
        return $this->photoExtension;
    }

    /**
     * Set altPhoto
     *
     * @param string $altPhoto
     *
     * @return Observation
     */
    public function setAltPhoto($altPhoto)
    {
        $this->altPhoto = $altPhoto;

        return $this;
    }

    /**
     * Get altPhoto
     *
     * @return string
     */
    public function getAltPhoto()
    {
        return $this->altPhoto;
    }

    /**
     * Set audioExtension
     *
     * @param string $audioExtension
     *
     * @return Observation
     */
    public function setAudioExtension($audioExtension)
    {
        $this->audioExtension = $audioExtension;

        return $this;
    }

    /**
     * Get audioExtension
     *
     * @return string
     */
    public function getAudioExtension()
    {
        return $this->audioExtension;
    }

    public function getPhotoFile()
    {
        return $this->photoFile;
    }

    public function setPhotoFile($photoFile)
    {
        $this->photoFile = $photoFile;
    }

    public function getAudioFile()
    {
        return $this->audioFile;
    }

    public function setAudioFile($audioFile)
    {
        $this->audioFile = $audioFile;
    }
}
