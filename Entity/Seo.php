<?php

namespace PN\SeoBundle\Entity;

use Doctrine\ORM\Mapping\UniqueConstraint;
use Doctrine\ORM\Mapping as ORM;
use PN\SeoBundle\Model\SeoModel;
use PN\SeoBundle\Model\SeoInterface;

/**
 * Seo
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table("seo", uniqueConstraints={@UniqueConstraint(name="slug_unique", columns={"slug", "seo_base_route_id"})})
 * @ORM\Entity(repositoryClass="PN\SeoBundle\Repository\SeoRepository")
 */
class Seo extends SeoModel implements SeoInterface {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

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

}
