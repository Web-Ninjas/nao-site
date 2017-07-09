<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Page
 *
 * @ORM\Table(name="page")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Page
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
     * @ORM\Column(name="nameIdentifier", type="string", length=255, unique=true)
    */
    private $nameIdentifier;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastUpdate", type="datetime")
     */
    private $lastUpdate;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;


    
    // On ajoute cet attribut pour y stocker le nom du fichier temporairement
    private $tempFilename;
    
    /**
     * @var string
     *
     * @ORM\Column(name="photo", type="string", length=255, nullable=true)
     * @Assert\File(mimeTypes={ "image/jpeg", "image/png", "image/jpg"})
     * @Assert\Image
     */
    private $photoExtension;


    /**
     * @ORM\Column(name="altPhoto", type="string", length=255, nullable=true)
     */
    private $altPhoto;
    
    /**
     * @Assert\File(mimeTypes={ "image/jpeg", "image/png", "image/jpg"})
     */
    private $photoBanner;


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
     * Set lastUpdate
     *
     * @param \DateTime $lastUpdate
     *
     * @return Page
     */
    public function setLastUpdate($lastUpdate)
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    /**
     * Get lastUpdate
     *
     * @return \DateTime
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Page
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Page
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



    public function setPhotoBanner(UploadedFile $photoBanner = null)
    {
        $this->photoBanner = $photoBanner;

        // On vérifie si on avait déjà un fichier pour cette entité
        if (null !== $this->photoExtension) {
            // On sauvegarde l'extension du fichier pour le supprimer plus tard
            $this->tempFilename = $this->photoExtension;

            // On réinitialise les valeurs des attributs photoExtension et altPhoto
            $this->photoExtension = null;
            $this->altPhoto = null;
        }
    }

    /**
     * Get photoBanner
     *
     * @return string
     */
    public function getPhotoBanner()
    {
        return $this->photoBanner;
    }

    public function getNameIdentifier()
    {
        return $this->nameIdentifier;
    }

    public function setNameIdentifier($nameIdentifier)
    {
        $this->nameIdentifier = $nameIdentifier;
    }

    /**
     * Set photo
     *
     * @param string $photo
     *
     * @return Article
     */
    public function setPhotoExtension($photoExtension)
    {
        $this->photoExtension = $photoExtension;

        return $this;
    }

    /**
     * Get photo
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
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        // Si jamais il n'y a pas de fichier (champ facultatif), on ne fait rien
        if (null === $this->photoBanner) {
            return;
        }

        // Le nom du fichier est son id, on doit juste stocker également son extension
        // Pour faire propre, on devrait renommer cet attribut en « extension », plutôt que « photoExtension »
        $this->photoExtension = $this->photoBanner->guessExtension();

        // Et on génère l'attribut altPhoto de la balise <img>, à la valeur du nom du fichier sur le PC de l'internaute
        $this->altPhoto = $this->photoBanner->getClientOriginalName();
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        // Si jamais il n'y a pas de fichier (champ facultatif), on ne fait rien
        if (null === $this->photoBanner) {
            return;
        }

        // Si on avait un ancien fichier, on le supprime
        if (null !== $this->tempFilename) {
            $oldFile = $this->getUploadRootDir().'/'.$this->id.'.'.$this->tempFilename;
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        // On déplace le fichier envoyé dans le répertoire de notre choix
        $this->photoBanner->move(
            $this->getUploadRootDir(), // Le répertoire de destination
            $this->id.'.'.$this->photoExtension   // Le nom du fichier à créer, ici « id.extension »
        );
    }

    /**
     * @ORM\PreRemove()
     */
    public function preRemoveUpload()
    {
        // On sauvegarde temporairement le nom du fichier, car il dépend de l'id
        $this->tempFilename = $this->getUploadRootDir().'/'.$this->id.'.'.$this->photoExtension;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        // En PostRemove, on n'a pas accès à l'id, on utilise notre nom sauvegardé
        if (file_exists($this->tempFilename))
        {
            // On supprime le fichier
            unlink($this->tempFilename);
        }
    }

    public function getUploadDir()
    {
        // On retourne le chemin relatif vers l'image pour un navigateur (relatif au répertoire /web donc)
        return 'uploads/pages';
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
}

