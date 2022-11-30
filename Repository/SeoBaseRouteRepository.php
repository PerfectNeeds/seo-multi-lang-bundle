<?php

namespace PN\SeoBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use PN\SeoBundle\Entity\SeoBaseRoute;
use PN\ServiceBundle\Utils\Validate;

class SeoBaseRouteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SeoBaseRoute::class);
    }

    public function findByEntity($entity, $error = true): ?SeoBaseRoute
    {
        $entityName = (new \ReflectionClass($entity))->getName();
        $seoBaseRoute = $this->findOneBy(["entityName" => $entityName]);

        if (!$seoBaseRoute instanceof SeoBaseRoute) {
            $entityName = (new \ReflectionClass($entity))->getShortName();
            $seoBaseRoute = $this->findOneBy(["entityName" => $entityName]);
        }

        if (!$seoBaseRoute and $error == true) {
            throw new \Exception("Can't find SeoBaseRoute");
        }

        return $seoBaseRoute;
    }

    private function getStatement(): QueryBuilder
    {
        return $this->createQueryBuilder('t');
    }

    private function filterOrder(QueryBuilder $statement, \stdClass $search): void
    {
        $sortSQL = [
            't.entityName',
            't.baseRoute',
            't.created',
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

    private function filterWhereClause(QueryBuilder $statement, \stdClass $search): void
    {
        if (isset($search->string) and Validate::not_null($search->string)) {
            $statement->andWhere('t.id LIKE :searchTerm '
                .'OR t.entityName LIKE :searchTerm '
                .'OR t.baseRoute LIKE :searchTerm '
            );
            $statement->setParameter('searchTerm', '%'.trim($search->string).'%');
        }
    }

    private function filterPagination(QueryBuilder $statement, $startLimit = null, $endLimit = null): void
    {
        if ($startLimit !== null and $endLimit !== null) {
            $statement->setFirstResult($startLimit)
                ->setMaxResults($endLimit);
        }
    }

    private function filterCount(QueryBuilder $statement): int
    {
        $statement->select("COUNT(DISTINCT t.id)");
        $statement->setMaxResults(1);

        $count = $statement->getQuery()->getOneOrNullResult();
        if (is_array($count) and count($count) > 0) {
            return (int)reset($count);
        }

        return 0;
    }

    public function filter($search, $count = false, $startLimit = null, $endLimit = null): int|array
    {
        $statement = $this->getStatement();
        $this->filterWhereClause($statement, $search);

        if ($count == true) {
            return $this->filterCount($statement);
        }

        $statement->groupBy('t.id');
        $this->filterPagination($statement, $startLimit, $endLimit);
        $this->filterOrder($statement, $search);

        return $statement->getQuery()->execute();
    }
}
