<?php

namespace PN\SeoBundle\Entity;

use PN\ServiceBundle\Model\DateTimeTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * SeoBaseRoute
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table("seo_base_route")
 * @ORM\Entity(repositoryClass="PN\SeoBundle\Repository\SeoBaseRouteRepository")
 * @UniqueEntity("entityName",message="This entity name is used before.")
 */
class SeoBaseRoute {

    use DateTimeTrait;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="entity_name", type="string", unique=true)
     */
    protected $entityName;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="base_route", type="string")
     */
    protected $baseRoute;

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
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set entityName
     *
     * @param string $entityName
     *
     * @return SeoBaseRoute
     */
    public function setEntityName($entityName) {
        $this->entityName = $entityName;

        return $this;
    }

    /**
     * Get entityName
     *
     * @return string
     */
    public function getEntityName() {
        return $this->entityName;
    }

    /**
     * Set baseRoute
     *
     * @param string $baseRoute
     *
     * @return SeoBaseRoute
     */
    public function setBaseRoute($baseRoute) {
        $this->baseRoute = rtrim($baseRoute, "/");

        return $this;
    }

    /**
     * Get baseRoute
     *
     * @return string
     */
    public function getBaseRoute() {
        return $this->baseRoute;
    }

}
