<?php

namespace PNSeoBundle\Model;

trait SeoDeleteTrait {

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $deleted = null;

    /**
     * @ORM\Column(name="deleted_by", type="string", length=30, nullable=true)
     */
    protected $deletedBy = NULL;

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Agent
     */
    public function setDeleted($deleted) {
        $this->deleted = $deleted;
        if (method_exists($this, 'getSeo') == true AND $this->getSeo() != NULL) {
            $this->getSeo()->setDeleted(true);
            $rand = substr(md5(microtime()), rand(0, 26), 5);
            $this->getSeo()->SetSlug($this->getSeo()->getSlug() . '-del-' . $rand);
        }

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean
     */
    public function getDeleted() {
        return $this->deleted;
    }

    /**
     * Set deletedBy
     *
     * @param string $deletedBy
     * @return Agent
     */
    public function setDeletedBy($deletedBy) {
        $this->deletedBy = $deletedBy;

        return $this;
    }

    /**
     * Get deletedBy
     *
     * @return string
     */
    public function getDeletedBy() {
        return $this->deletedBy;
    }

}
