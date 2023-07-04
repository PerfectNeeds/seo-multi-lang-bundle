<?php

namespace PN\SeoBundle\Entity;

use App\SeoBundle\Entity\Seo;
use Doctrine\ORM\Mapping as ORM;
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
class SeoPage
{
    use DateTimeTrait;

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
