<?php

namespace PN\SeoBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use PN\ServiceBundle\Utils\SQL;
use PN\ServiceBundle\Utils\Validate;

/**
 * SeoPageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SeoPageRepository extends \Doctrine\ORM\EntityRepository
{

    public function find($id, $lockMode = null, $lockVersion = null)
    {
        $statement = $this->getStatement();
        $statement->andWhere("sp.id=:id")
            ->setParameter("id", $id);

        return $statement->getQuery()->getOneOrNullResult();
    }

    private function getStatement()
    {
        return $this->createQueryBuilder('sp')
            ->addSelect("seo")
            ->addSelect("seoTrans")
            ->addSelect("l")
            ->addSelect("seoSocials")
            ->leftJoin("sp.seo", "seo")
            ->leftJoin("seo.translations", "seoTrans")
            ->leftJoin("seoTrans.language", "l")
            ->leftJoin("seo.seoSocials", "seoSocials");
    }

    private function filterOrder(QueryBuilder $statement, \stdClass $search)
    {
        $sortSQL = [
            'sp.id',
            'sp.title',
            'sp.created',
        ];

        if (isset($search->ordr) and Validate::not_null($search->ordr)) {
            $dir = $search->ordr['dir'];
            $columnNumber = $search->ordr['column'];
            if (isset($columnNumber) and array_key_exists($columnNumber, $sortSQL)) {
                $statement->addOrderBy($sortSQL[$columnNumber], $dir);
            }
        } else {
            $statement->addOrderBy($sortSQL[1]);
        }
    }

    private function filterWhereClause(QueryBuilder $statement, \stdClass $search)
    {
        if (isset($search->string) and Validate::not_null($search->string)) {
            $statement->andWhere('sp.id LIKE :searchTerm '
                .'OR sp.title LIKE :searchTerm '
            );
            $statement->setParameter('searchTerm', '%'.trim($search->string).'%');
        }
    }

    private function filterPagination(QueryBuilder $statement, $startLimit = null, $endLimit = null)
    {
        if ($startLimit === null or $endLimit === null) {
            return false;
        }
        $statement->setFirstResult($startLimit)
            ->setMaxResults($endLimit);
    }

    private function filterCount(QueryBuilder $statement)
    {
        $statement->select("COUNT(DISTINCT sp.id)");
        $statement->setMaxResults(1);

        $count = $statement->getQuery()->getOneOrNullResult();
        if (is_array($count) and count($count) > 0) {
            return (int)reset($count);
        }

        return 0;
    }

    public function filter($search, $count = false, $startLimit = null, $endLimit = null)
    {
        $statement = $this->getStatement();
        $this->filterWhereClause($statement, $search);

        if ($count) {
            return $this->filterCount($statement);
        }

        $statement->groupBy('sp.id');
        $this->filterPagination($statement, $startLimit, $endLimit);
        $this->filterOrder($statement, $search);

        return $statement->getQuery()->execute();
    }

}
