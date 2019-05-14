<?php

namespace PN\SeoBundle\Model;

trait SeoTrait {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="\PN\SeoBundle\Entity\SeoSocial", cascade={"persist", "remove" })
     */
    protected $seoSocials;

    /**
     * @ORM\OneToMany(targetEntity="PN\Bundle\SeoBundle\Entity\Translation\SeoTranslation", mappedBy="translatable", cascade={"ALL"}, orphanRemoval=true)
     */
    protected $translations;

    /**
     * Add seoSocial
     *
     * @param \PN\SeoBundle\Entity\SeoSocial $seoSocial
     *
     * @return Seo
     */
    public function addSeoSocial(\PN\SeoBundle\Entity\SeoSocial $seoSocial) {
        if (!$this->seoSocials instanceof \Doctrine\ORM\PersistentCollection AND ! $this->seoSocials instanceof \Doctrine\Common\Collections\ArrayCollection) {
            throw new \Exception('Error: Add $this->seoSocials = new \Doctrine\Common\Collections\ArrayCollection() to ' . __CLASS__ . '::__construct() method');
        }
        if (!$this->seoSocials->contains($seoSocial)) {
            $this->seoSocials->add($seoSocial);
            $seoSocial->setSeo($this);
        }

        return $this;
    }

    /**
     * Remove seoSocial
     *
     * @param \PN\SeoBundle\Entity\SeoSocial $seoSocial
     */
    public function removeSeoSocial(\PN\SeoBundle\Entity\SeoSocial $seoSocial) {
        if (!$this->seoSocials instanceof \Doctrine\ORM\PersistentCollection AND ! $this->seoSocials instanceof \Doctrine\Common\Collections\ArrayCollection) {
            throw new \Exception('Error: Add $this->translations = new \Doctrine\Common\Collections\ArrayCollection() to ' . __CLASS__ . '::__construct() method');
        }
        $this->seoSocials->removeElement($seoSocial);
        $seoSocial->setSeo(null);
    }

    /**
     * Get seoSocials
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSeoSocials($types = null) {
        if ($types) {
            return $this->seoSocials->filter(function ( $seoSocial) use ($types) {
                        return in_array($seoSocial->getSocialNetwork(), $types);
                    });
        } else {
            return $this->seoSocials;
        }
    }

    /**
     * Get seoSocial By Type
     *
     * @return \PN\SeoBundle\Entity\SeoSocial
     */
    public function getSeoSocialByType($type) {
        return $this->getSeoSocials(array($type))->first();
    }

}
