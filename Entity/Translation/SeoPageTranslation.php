<?php

namespace PN\SeoBundle\Entity\Translation;

use Doctrine\ORM\Mapping as ORM;
use PN\LocaleBundle\Model\EditableTranslation;
use PN\LocaleBundle\Model\TranslationEntity;

/**
 * @ORM\MappedSuperclass
 */
class SeoPageTranslation extends TranslationEntity implements EditableTranslation {

    /**
     * @ORM\Column(name="brief", type="text", nullable=true)
     */
    private ?string $brief = null;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="PN\SeoBundle\Entity\SeoPage", inversedBy="translations")
     */
    protected $translatable;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="PN\LocaleBundle\Entity\Language")
     */
    protected $language;
    public function setBrief(?string $brief): self
    {
        $this->brief = $brief;

        return $this;
    }

    public function getBrief(): ?string
    {
        return $this->brief;
    }

}
