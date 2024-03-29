<?php

namespace PN\SeoBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use VM5\EntityTranslationsBundle\Model\Translatable;
use PN\LocaleBundle\Model\LocaleTrait;

/**
 * @ORM\MappedSuperclass
 */
abstract class Seo implements Translatable {

    use LocaleTrait;

    /**
     * @ORM\ManyToOne(targetEntity="PN\SeoBundle\Entity\SeoBaseRoute")
     */
    protected $seoBaseRoute;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="slug", type="string", length=255)
     */
    protected $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_description", type="text", nullable=true)
     */
    protected $metaDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="focus_keyword", type="string", length=255, nullable=true)
     */
    protected $focusKeyword;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_keyword", type="string" , nullable=true)
     */
    protected $metaKeyword;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_tags", type="text" , nullable=true)
     */
    protected $metaTags;

    /**
     * @var integer
     *
     * @ORM\Column(name="state", type="smallint", nullable=true)
     */
    protected $state;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $lastModified;

    /**
     * @ORM\Column(name="deleted", type="boolean", options={"default" = 0}),nullable=false)
     */
    protected $deleted = false;


    public function __clone() {
        $this->id = NULL;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->seoSocials = new \Doctrine\Common\Collections\ArrayCollection();
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Now we tell doctrine that before we persist or update we call the updatedTimestamps() function.
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps() {
        $this->setLastModified(new \DateTime(date('Y-m-d H:i:s')));
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Seo
     */
    public function setSlug($slug) {
        $this->slug = $slug;
        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug() {
        return !$this->currentTranslation ? $this->slug : $this->currentTranslation->getSlug();
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Seo
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
     * Set metaDescription
     *
     * @param string $metaDescription
     * @return Seo
     */
    public function setMetaDescription($metaDescription) {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * Get metaDescription
     *
     * @return string
     */
    public function getMetaDescription() {
        return !$this->currentTranslation ? $this->metaDescription : $this->currentTranslation->getMetaDescription();
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Seo
     */
    public function setDeleted($deleted) {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean
     */
    public function getDeleted() {
        return $this->deleted;
    }

    /**
     * Set focusKeyword
     *
     * @param string $focusKeyword
     *
     * @return Seo
     */
    public function setFocusKeyword($focusKeyword) {
        $this->focusKeyword = $focusKeyword;

        return $this;
    }

    /**
     * Get focusKeyword
     *
     * @return string
     */
    public function getFocusKeyword() {
        return !$this->currentTranslation ? $this->focusKeyword : $this->currentTranslation->getFocusKeyword();
    }

    /**
     * Set metaKeyword
     *
     * @param string $metaKeyword
     *
     * @return Seo
     */
    public function setMetaKeyword($metaKeyword) {
        $this->metaKeyword = $metaKeyword;

        return $this;
    }

    /**
     * Get metaKeyword
     *
     * @return string
     */
    public function getMetaKeyword() {
        return !$this->currentTranslation ? $this->metaKeyword : $this->currentTranslation->getMetaKeyword();
    }

    /**
     * Set lastModified
     *
     * @param \DateTime $lastModified
     * @return Seo
     */
    public function setLastModified($lastModified) {
        $this->lastModified = $lastModified;

        return $this;
    }

    /**
     * Get lastModified
     *
     * @return \DateTime
     */
    public function getLastModified() {
        return $this->lastModified;
    }

    /**
     * Set state
     *
     * @param integer $state
     *
     * @return Seo
     */
    public function setState($state) {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return integer
     */
    public function getState() {
        return !$this->currentTranslation ? $this->state : $this->currentTranslation->getState();
    }

    /**
     * Set seoBaseRoute
     *
     * @param \PN\SeoBundle\Entity\SeoBaseRoute $seoBaseRoute
     *
     * @return Seo
     */
    public function setSeoBaseRoute(\PN\SeoBundle\Entity\SeoBaseRoute $seoBaseRoute = null) {
        $this->seoBaseRoute = $seoBaseRoute;

        return $this;
    }

    /**
     * Get seoBaseRoute
     *
     * @return \PN\SeoBundle\Entity\SeoBaseRoute
     */
    public function getSeoBaseRoute() {
        return $this->seoBaseRoute;
    }

    /**
     * Set metaTags
     *
     * @param string $metaTags
     * @return SeoTranslation
     */
    public function setMetaTags($metaTags) {
        $this->metaTags = $metaTags;

        return $this;
    }

    /**
     * Get metaTags
     *
     * @return string
     */
    public function getMetaTags() {
        return !$this->currentTranslation ? $this->metaTags : $this->currentTranslation->getMetaTags();
    }

}
