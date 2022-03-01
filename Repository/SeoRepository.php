<?php

namespace PN\SeoBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PN\SeoBundle\Entity\Seo;

class SeoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, string $class = Seo::class)
    {
        parent::__construct($registry, $class);
    }

    public function findBySlugAndBaseRouteAndNotId($seoBaseRouteId, $slug, $seoId)
    {
        return $this->createQueryBuilder("s")
            ->andWhere("s.deleted = 0")
            ->andWhere("s.slug = :slug")
            ->andWhere("s.seoBaseRoute = :seoBaseRouteId")
            ->andWhere("s.id != :seoId ")
            ->setParameter("slug", $slug)
            ->setParameter("seoBaseRouteId", $seoBaseRouteId)
            ->setParameter("seoId", $seoId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findTranBySlugAndBaseRouteAndNotId($seoBaseRouteId, $slug, $seoId, $locale)
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.translations', 'st')
            ->leftJoin('st.language', 'l')
            ->andWhere("s.seoBaseRoute = :seoBaseRouteId")
            ->andWhere("l.locale = :locale")
            ->andWhere("st.slug = :slug")
            ->andWhere("s.id != :seoId")
            ->andWhere("s.deleted=0")
            ->setParameter("seoBaseRouteId", $seoBaseRouteId)
            ->setParameter("slug", $slug)
            ->setParameter("locale", $locale)
            ->setParameter("seoId", $seoId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findTranBySlugAndBaseRoute($seoBaseRouteId, $slug, $locale)
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.translations', 'st')
            ->leftJoin('st.language', 'l')
            ->andWhere("s.seoBaseRoute = :seoBaseRouteId")
            ->andWhere("l.locale = :locale")
            ->andWhere("st.slug = :slug")
            ->andWhere("s.deleted=0")
            ->setParameter("seoBaseRouteId", $seoBaseRouteId)
            ->setParameter("slug", $slug)
            ->setParameter("locale", $locale)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneBySlugAndBaseRoute($slug, $seoBaseRouteId)
    {
        return $this->createQueryBuilder('s')
            ->andWhere("s.seoBaseRoute = :seoBaseRouteId")
            ->andWhere("s.slug = :slug")
            ->setParameter("seoBaseRouteId", $seoBaseRouteId)
            ->setParameter("slug", $slug)
            ->andWhere("s.deleted=0")
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByFocusKeywordAndNotId($focusKeyword, $seoId)
    {
        return $this->createQueryBuilder("s")
            ->andWhere("s.deleted = 0")
            ->andWhere("s.focusKeyword = :focusKeyword")
            ->andWhere("s.id != :seoId ")
            ->setParameter("focusKeyword", $focusKeyword)
            ->setParameter("seoId", $seoId)
            ->getQuery()
            ->getResult();
    }

    public function findOneSeo($seoBaseRouteId, $slug)
    {
        return $this->createQueryBuilder("s")
            ->leftJoin("s.translations", "st")
            ->andWhere("s.deleted = 0")
            ->andWhere("s.seoBaseRoute = :seoBaseRouteId")
            ->andWhere("st.slug = :slug OR s.slug = :slug")
            ->setParameter("seoBaseRouteId", $seoBaseRouteId)
            ->setParameter("slug", $slug)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneSeoByLocale($seoBaseRouteId, $slug, $locale)
    {
        return $this->createQueryBuilder("s")
            ->leftJoin("s.translations", "st")
            ->leftJoin("st.language", "l")
            ->andWhere("s.deleted = 0")
            ->andWhere("s.seoBaseRoute = :seoBaseRouteId")
            ->andWhere("st.slug = :slug OR s.slug = :slug")
            ->andWhere("l.locale = :locale")
            ->setParameter("seoBaseRouteId", $seoBaseRouteId)
            ->setParameter("slug", $slug)
            ->setParameter("locale", $locale)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
