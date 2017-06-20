<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OiseauTaxref
 *
 * @ORM\Table(name="oiseau_taxref")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OiseauTaxrefRepository")
 */
class OiseauTaxref
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
     * @var string
     *
     * @ORM\Column(name="ordre", type="string", length=255, nullable=true)
     */
    private $ordre;

    /**
     * @var string
     *
     * @ORM\Column(name="famille", type="string", length=255, nullable=true)
     */
    private $famille;

    /**
     * @var string
     *
     * @ORM\Column(name="lb_auteur", type="string", length=255, nullable=true)
     */
    private $lbAuteur;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_complet", type="string", length=255)
     */
    private $nomComplet;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_complet_html", type="string", length=255)
     */
    private $nomCompletHtml;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_valide", type="string", length=255)
     */
    private $nomValide;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_vern", type="string", length=255, nullable=true)
     */
    private $nomVern;

    /**
     * @var string
     *
     * @ORM\Column(name="habitat", type="string", nullable=true)
     */
    private $habitat;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;


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
     * Set ordre
     *
     * @param string $ordre
     *
     * @return OiseauTaxref
     */
    public function setOrdre($ordre)
    {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * Get ordre
     *
     * @return string
     */
    public function getOrdre()
    {
        return $this->ordre;
    }

    /**
     * Set famille
     *
     * @param string $famille
     *
     * @return OiseauTaxref
     */
    public function setFamille($famille)
    {
        $this->famille = $famille;

        return $this;
    }

    /**
     * Get famille
     *
     * @return string
     */
    public function getFamille()
    {
        return $this->famille;
    }

    /**
     * Set lbAuteur
     *
     * @param string $lbAuteur
     *
     * @return OiseauTaxref
     */
    public function setLbAuteur($lbAuteur)
    {
        $this->lbAuteur = $lbAuteur;

        return $this;
    }

    /**
     * Get lbAuteur
     *
     * @return string
     */
    public function getLbAuteur()
    {
        return $this->lbAuteur;
    }

    /**
     * Set nomComplet
     *
     * @param string $nomComplet
     *
     * @return OiseauTaxref
     */
    public function setNomComplet($nomComplet)
    {
        $this->nomComplet = $nomComplet;

        return $this;
    }

    /**
     * Get nomComplet
     *
     * @return string
     */
    public function getNomComplet()
    {
        return $this->nomComplet;
    }

    /**
     * Set nomCompletHtml
     *
     * @param string $nomCompletHtml
     *
     * @return OiseauTaxref
     */
    public function setNomCompletHtml($nomCompletHtml)
    {
        $this->nomCompletHtml = $nomCompletHtml;

        return $this;
    }

    /**
     * Get nomCompletHtml
     *
     * @return string
     */
    public function getNomCompletHtml()
    {
        return $this->nomCompletHtml;
    }

    /**
     * Set nomValide
     *
     * @param string $nomValide
     *
     * @return OiseauTaxref
     */
    public function setNomValide($nomValide)
    {
        $this->nomValide = $nomValide;

        return $this;
    }

    /**
     * Get nomValide
     *
     * @return string
     */
    public function getNomValide()
    {
        return $this->nomValide;
    }

    /**
     * Set nomVern
     *
     * @param string $nomVern
     *
     * @return OiseauTaxref
     */
    public function setNomVern($nomVern)
    {
        $this->nomVern = $nomVern;

        return $this;
    }

    /**
     * Get nomVern
     *
     * @return string
     */
    public function getNomVern()
    {
        return $this->nomVern;
    }

    /**
     * Set habitat
     *
     * @param integer $habitat
     *
     * @return OiseauTaxref
     */
    public function setHabitat($habitat)
    {
        $this->habitat = $habitat;

        return $this;
    }

    /**
     * Get habitat
     *
     * @return int
     */
    public function getHabitat()
    {
        return $this->habitat;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return OiseauTaxref
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}

