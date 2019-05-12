<?php

namespace PNSeoBundle\Entity\Translation;

use Doctrine\ORM\Mapping as ORM;
use VM5\EntityTranslationsBundle\Model\EditableTranslation;
use PNLocaleBundle\Model\TranslationEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="seo_translations")
 */
class SeoTranslation extends TranslationEntity implements EditableTranslation {

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @var string
     * @ORM\Column(name="slug", type="string", length=255)
     */
    protected $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_description", type="text" , nullable=true)
     */
    protected $metaDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="focus_keyword", type="string" , nullable=true)
     */
    protected $focusKeyword;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_keyword", type="string" , nullable=true)
     */
    protected $metaKeyword;

    /**
     * @var integer
     *
     * @ORM\Column(name="state", type="smallint", nullable=true)
     */
    protected $state;

    /**
     * @var
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="PNSeoBundle\Entity\Seo", inversedBy="translations")
     */
    protected $translatable;

    /**
     * @var Language
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="PNLocaleBundle\Entity\Language")
     */
    protected $language;

    /**
     * Set title
     *
     * @param string $title
     *
     * @return SeoTranslation
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
        return $this->title;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return SeoTranslation
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
        return $this->slug;
    }

    /**
     * Set metaDescription
     *
     * @param string $metaDescription
     *
     * @return SeoTranslation
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
        return $this->metaDescription;
    }

    /**
     * Set focusKeyword
     *
     * @param string $focusKeyword
     *
     * @return SeoTranslation
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
        return $this->focusKeyword;
    }

    /**
     * Set metaKeyword
     *
     * @param string $metaKeyword
     *
     * @return SeoTranslation
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
        return $this->metaKeyword;
    }

    /**
     * Set state
     *
     * @param integer $state
     *
     * @return SeoTranslation
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
        return $this->state;
    }

}
