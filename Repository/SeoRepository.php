<?php

namespace PN\SeoBundle\Repository;

use Doctrine\ORM\EntityRepository;

class SeoRepository extends EntityRepository
{

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

    /**
     * @param object $entity
     * @return array
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getSeoForSitemap($entity)
    {
        $entityName = (new \ReflectionClass($entity))->getShortName();
        $sql = "SELECT s.slug, s.last_modified FROM seo s LEFT JOIN seo_base_route sbr ON sbr.id=s.seo_base_route_id WHERE s.deleted = 0 AND sbr.entity_name=:entityName";
        $connection = $this->getEntityManager()->getConnection();
        $statement = $connection->prepare($sql);
        $statement->bindValue("entityName", $entityName);

        return $statement->executeQuery()->fetchAllAssociative();
    }

    /**
     * @param object $entity
     * @param string $locale (ex. ar, fr, de)
     * @return array
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getSeoForSitemapByLang($entity, $locale)
    {
        $entityName = (new \ReflectionClass($entity))->getShortName();
        $sql = "SELECT st.slug, s.last_modified FROM seo_translations st "
            ."LEFT JOIN seo s ON s.id=st.translatable_id "
            ."LEFT JOIN `language` l ON l.id=st.language_id "
            ."LEFT JOIN seo_base_route sbr ON sbr.id=s.seo_base_route_id "
            ."WHERE s.deleted = 0 AND l.locale=:locale AND sbr.entity_name=:entityName";
        $connection = $this->getEntityManager()->getConnection();
        $statement = $connection->prepare($sql);
        $statement->bindValue("entityName", $entityName);
        $statement->bindValue("locale", $locale);

        return $statement->executeQuery()->fetchAllAssociative();
    }

}
