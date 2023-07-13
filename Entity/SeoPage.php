<?php

namespace PN\SeoBundle\Entity;

use App\SeoBundle\Entity\Seo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use PN\LocaleBundle\Model\LocaleTrait;
use PN\LocaleBundle\Model\Translatable;
use PN\ServiceBundle\Model\DateTimeTrait;
use PN\ServiceBundle\Utils\General;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SeoPage
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="seo_page")
 * @ORM\Entity(repositoryClass="PN\SeoBundle\Repository\SeoPageRepository")
 * @UniqueEntity(
 *     fields={"type"},
 *     errorPath="type",
 *     message="This type is already exist."
 * )
 */
class SeoPage implements Translatable
{
    use DateTimeTrait,
        LocaleTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id = null;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="title", type="string", length=100)
     */
    private ?string $title = null;

    /**
     * @ORM\Column(name="brief", type="text", nullable=true)
     */
    private ?string $brief = null;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, unique=true)
     */
    private ?string $type = null;

    /**
     * @ORM\OneToOne(targetEntity="\PN\SeoBundle\Entity\Seo", cascade={"persist", "remove" }, fetch="EAGER")
     */
    private ?Seo $seo = null;

    /**
     * @ORM\OneToMany(targetEntity="PN\SeoBundle\Entity\Translation\SeoPageTranslation", mappedBy="translatable", cascade={"ALL"}, orphanRemoval=true, fetch="LAZY")
     */
    private Collection $translations;

    /**
     * Now we tell doctrine that before we persist or update we call the updatedTimestamps() function.
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->setModified(new \DateTime(date('Y-m-d H:i:s')));

        if ($this->getCreated() == null) {
            $this->setCreated(new \DateTime(date('Y-m-d H:i:s')));
        }
        if ($this->getType() == null) {
            $this->setType(General::seoUrl($this->getTitle()));
        }
    }

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getBrief(): ?string
    {
        return !$this->currentTranslation ? $this->brief : $this->currentTranslation->getBrief();
    }

    public function setBrief(string $brief): self
    {
        $this->brief = $brief;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getSeo(): ?Seo
    {
        return $this->seo;
    }

    public function setSeo(?Seo $seo): self
    {
        $this->seo = $seo;

        return $this;
    }


}
