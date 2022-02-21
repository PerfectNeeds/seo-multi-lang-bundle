<?php

namespace PN\SeoBundle\Repository;

use Doctrine\ORM\EntityRepository;

class SeoRepository extends EntityRepository
{

    public function findBySlugAndBaseRouteAndNotId($seoBaseRouteId, $slug, $seoId)
    {
        $connection = $this->getEntityManager()->getConnection();
        $sql = "SELECT id FROM seo WHERE slug = :slug AND seo_base_route_id=:seoBaseRouteId AND id != :seoId AND deleted=:deleted";

        $statement = $connection->prepare($sql);
        $statement->bindValue("slug", $slug);
        $statement->bindValue("seoBaseRouteId", $seoBaseRouteId);
        $statement->bindValue("seoId", $seoId);
        $statement->bindValue("deleted", false);

        $queryResult = $statement->executeQuery()->fetchOne();
        if (!$queryResult) {
            return null;
        }

        return $this->find($queryResult);
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


        /* $connection = $this->getEntityManager()->getConnection();
         $sql = "SELECT s.id FROM seo_translations st "
                 . "LEFT JOIN seo s ON s.id=st.translatable_id "
                 . "LEFT JOIN `language` l ON l.id=st.language_id "
                 . "WHERE s.seo_base_route_id=:seoBaseRouteId AND l.locale=:locale AND st.slug =:slug AND s.id != :seoId AND s.deleted=:deleted "
                 . "LIMIT 1";
         $statement = $connection->prepare($sql);
         $statement->bindValue("slug", $slug);
         $statement->bindValue("seoBaseRouteId", $seoBaseRouteId);
         $statement->bindValue("seoId", $seoId);
         $statement->bindValue("locale", $locale);
         $statement->bindValue("deleted", FALSE);
         $statement->execute();

         $queryResult = $statement->fetchColumn();
         if (!$queryResult) {
             return null;
         }
         return $this->find($queryResult);*/
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

        /* $connection = $this->getEntityManager()->getConnection();
         $sql = "SELECT s.id FROM seo_translations st "
                 . "LEFT JOIN seo s ON s.id=st.translatable_id "
                 . "LEFT JOIN `language` l ON l.id=st.language_id "
                 . "WHERE s.seo_base_route_id=:seoBaseRouteId AND l.locale=:locale AND st.slug =:slug AND s.deleted=:deleted "
                 . "LIMIT 1";

         $statement = $connection->prepare($sql);
         $statement->bindValue("slug", $slug);
         $statement->bindValue("seoBaseRouteId", $seoBaseRouteId);
         $statement->bindValue("locale", $locale);
         $statement->bindValue("deleted", FALSE);
         $statement->execute();

         $queryResult = $statement->fetchColumn();
         if (!$queryResult) {
             return null;
         }
         return $this->find($queryResult);*/
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

       /* $connection = $this->getEntityManager()->getConnection();
        $sql = "SELECT id FROM seo WHERE slug = :slug AND seo_base_route_id=:seoBaseRouteId AND deleted=:deleted Limit 1";

        $statement = $connection->prepare($sql);
        $statement->bindValue("slug", $slug);
        $statement->bindValue("seoBaseRouteId", $seoBaseRouteId);
        $statement->bindValue("deleted", false);

        $statement->execute();

        $queryResult = $statement->fetchAll();

        $result = array();
        foreach ($queryResult as $key => $r) {
            $result = $this->find($r['id']);
        }

        return $result;*/
    }

    public function findByFocusKeywordAndNotId($focusKeyword, $seoId)
    {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT id FROM seo WHERE focus_keyword = :focusKeyword AND id != :seoId AND deleted=:deleted";

        $statement = $connection->prepare($sql);
        $statement->bindValue("focusKeyword", $focusKeyword);
        $statement->bindValue("seoId", $seoId);
        $statement->bindValue("deleted", false);

        $queryResult = $statement->executeQuery()->fetchAllAssociative();

        $result = array();
        foreach ($queryResult as $key => $r) {
            $result[$key] = $this->find($r['id']);
        }

        return $result;
    }

    public function findOneSeo($seoBaseRouteId, $slug)
    {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT s.id FROM seo_translations st "
            ."LEFT JOIN seo s ON s.id=st.translatable_id "
            ."WHERE s.seo_base_route_id=:seoBaseRouteId AND (st.slug =:slug OR s.slug=:slug )"
            ."LIMIT 1";

        $statement = $connection->prepare($sql);
        $statement->bindValue("seoBaseRouteId", $seoBaseRouteId);
        $statement->bindValue("slug", $slug);

        $queryResult = $statement->executeQuery()->fetchOne();
        if (!$queryResult) {
            return null;
        }

        return $this->find($queryResult);
    }

    public function findOneSeoByLocale($seoBaseRouteId, $slug, $locale)
    {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT s.id FROM seo_translations st "
            ."LEFT JOIN seo s ON s.id=st.translatable_id "
            ."LEFT JOIN `language` l ON l.id=st.language_id "
            ."WHERE s.seo_base_route_id=:seoBaseRouteId AND st.slug =:slug AND l.locale=:locale "
            ."LIMIT 1";

        $statement = $connection->prepare($sql);
        $statement->bindValue("seoBaseRouteId", $seoBaseRouteId);
        $statement->bindValue("slug", $slug);
        $statement->bindValue("locale", $locale);

        $queryResult = $statement->executeQuery()->fetchOne();
        if (!$queryResult) {
            return null;
        }

        return $this->find($queryResult);
    }

}
