<?php

namespace PN\SeoBundle\Entity;

use PN\ServiceBundle\Model\DateTimeTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Redirect404
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table("redirect_404")
 * @ORM\Entity(repositoryClass="PN\SeoBundle\Repository\Redirect404Repository")
 */
class Redirect404 {

    use DateTimeTrait;

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Url()
     * @ORM\Column(name="`from`", type="text", nullable=false)
     */
    protected $from;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Url()
     * @ORM\Column(name="`to`", type="text", nullable=false)
     */
    protected $to;

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
     * Set from
     *
     * @param string $from
     *
     * @return Redirect404
     */
    public function setFrom($from) {
        $this->from = $from;

        return $this;
    }

    /**
     * Get from
     *
     * @return string
     */
    public function getFrom() {
        return $this->from;
    }

    /**
     * Set to
     *
     * @param string $to
     *
     * @return Redirect404
     */
    public function setTo($to) {
        $this->to = $to;

        return $this;
    }

    /**
     * Get to
     *
     * @return string
     */
    public function getTo() {
        return $this->to;
    }

}
