<?php

namespace PN\SeoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PN\ServiceBundle\Model\DateTimeTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SeoPage
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="seo_page")
 * @ORM\Entity(repositoryClass="PN\SeoBundle\Repository\SeoPageRepository")
 */
class SeoPage {

    use DateTimeTrait;

    const SEO_HOME = 1;
    const SEO_CONTACT_US = 2;

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
     * @Assert\NotBlank()
     * @ORM\Column(name="title", type="string", length=100)
     */
    private $title;

    /**
     * Now we tell doctrine that before we persist or update we call the updatedTimestamps() function.
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps() {
        $this->setModified(new \DateTime(date('Y-m-d H:i:s')));

        if ($this->getCreated() == null) {
            $this->setCreated(new \DateTime(date('Y-m-d H:i:s')));
        }
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
     * @return SeoPage
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

}
