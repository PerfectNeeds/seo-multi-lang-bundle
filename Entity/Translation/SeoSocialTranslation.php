<?php

namespace PN\SeoBundle\Entity\Translation;

use Doctrine\ORM\Mapping as ORM;
use VM5\EntityTranslationsBundle\Model\EditableTranslation;
use PN\LocaleBundle\Model\TranslationEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="seo_social_translations")
 */
class SeoSocialTranslation extends TranslationEntity implements EditableTranslation {

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255,nullable=true)
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
     * @var
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="PN\SeoBundle\Entity\SeoSocial", inversedBy="translations")
     */
    protected $translatable;

    /**
     * @var Language
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="PN\LocaleBundle\Entity\Language")
     */
    protected $language;

    /**
     * Set title
     *
     * @param string $title
     *
     * @return SeoSocialTranslation
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
     * Set description
     *
     * @param string $description
     *
     * @return SeoSocialTranslation
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
        return $this->description;
    }

    /**
     * Set imageUrl
     *
     * @param string $imageUrl
     *
     * @return SeoSocialTranslation
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
        return $this->imageUrl;
    }

}
