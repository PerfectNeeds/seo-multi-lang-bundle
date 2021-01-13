<?php

namespace PN\SeoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Arxy\EntityTranslationsBundle\Model\Translatable;
use PN\LocaleBundle\Model\LocaleTrait;

/**
 * SeoSocial
 *
 * @ORM\Table(name="seo_social")
 * @ORM\Entity(repositoryClass="PN\SeoBundle\Repository\SeoSocialRepository")
 */
class SeoSocial implements Translatable {

    use LocaleTrait;

    const FACEBOOK = 1;
    const TWITTER = 2;

    public static $socialNetworks = [
        ["name" => "Facebook", "icon" => "icon-facebook", "type" => self::FACEBOOK],
        ["name" => "Twitter", "icon" => "icon-twitter", "type" => self::TWITTER],
    ];

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
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text",nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="image_url", type="string", length=255,nullable=true)
     */
    private $imageUrl;

    /**
     * @var int
     *
     * @ORM\Column(name="social_network", type="smallint",nullable=true)
     */
    private $socialNetwork;

    /**
     * @ORM\OneToMany(targetEntity="PN\SeoBundle\Entity\Translation\SeoSocialTranslation", mappedBy="translatable", cascade={"ALL"}, orphanRemoval=true)
     */
    protected $translations;

    /**
     * Constructor
     */
    public function __construct() {
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return SeoSocial
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle() {
        return !$this->currentTranslation ? $this->title : $this->currentTranslation->getTitle();
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return SeoSocial
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription() {
        return !$this->currentTranslation ? $this->description : $this->currentTranslation->getDescription();
    }

    /**
     * Set imageUrl
     *
     * @param string $imageUrl
     *
     * @return SeoSocial
     */
    public function setImageUrl($imageUrl) {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    /**
     * Get imageUrl
     *
     * @return string
     */
    public function getImageUrl() {
        return !$this->currentTranslation ? $this->imageUrl : $this->currentTranslation->getImageUrl();
    }

    /**
     * Set socialNetwork
     *
     * @param integer $socialNetwork
     *
     * @return SeoSocial
     */
    public function setSocialNetwork($socialNetwork) {
        $this->socialNetwork = $socialNetwork;

        return $this;
    }

    /**
     * Get socialNetwork
     *
     * @return int
     */
    public function getSocialNetwork() {
        return $this->socialNetwork;
    }

    /**
     * Set seo
     *
     * @param \PN\SeoBundle\Entity\Seo $seo
     *
     * @return SeoSocial
     */
    public function setSeo(\PN\SeoBundle\Entity\Seo $seo = null) {
        $this->seo = $seo;

        return $this;
    }

    /**
     * Get seo
     *
     * @return \PN\SeoBundle\Entity\Seo
     */
    public function getSeo() {
        return $this->seo;
    }

}
