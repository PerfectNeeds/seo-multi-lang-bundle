<?php

namespace PN\SeoBundle\Repository;

use Doctrine\ORM\EntityRepository;
use PN\ServiceBundle\Utils\SQL;
use PN\ServiceBundle\Utils\Validate;

class SeoBaseRouteRepository extends EntityRepository
{

    public function findByEntity($entity, $error = true)
    {
        $entityName = (new \ReflectionClass($entity))->getShortName();
        $seoBaseRoute = $this->findOneBy(["entityName" => $entityName]);

        if (!$seoBaseRoute and $error == true) {
            throw new \Exception("Can't find SeoBaseRoute");
        }

        return $seoBaseRoute;
    }

    public function filter($search, $count = false, $startLimit = null, $endLimit = null)
    {

        $sortSQL = [
            0 => 'sb.entity_name',
            1 => 'sb.base_route',
            2 => 'sb.created',
        ];
        $connection = $this->getEntityManager()->getConnection();
        $where = false;
        $clause = '';

        $searchFiltered = new \stdClass();
        foreach ($search as $key => $value) {
            if (Validate::not_null($value) and !is_array($value)) {
                $searchFiltered->{$key} = substr($connection->quote($value), 1, -1);
            } else {
                $searchFiltered->{$key} = $value;
            }
        }


        if (isset($searchFiltered->string) and $searchFiltered->string) {

            if (SQL::validateSS($searchFiltered->string)) {
                $where = ($where) ? ' AND ( ' : ' WHERE ( ';
                $clause .= SQL::searchSCG($searchFiltered->string, 'sb.id', $where);
                $clause .= SQL::searchSCG($searchFiltered->string, 'sb.entity_name', ' OR ');
                $clause .= SQL::searchSCG($searchFiltered->string, 'sb.base_route', ' OR ');
                $clause .= " ) ";
            }
        }

        if ($count) {
            $sqlInner = "SELECT count(sb.id) as `count` FROM seo_base_route sb ";

            $statement = $connection->prepare($sqlInner);

            return $statement->executeQuery()->fetchOne();
        }
        //----------------------------------------------------------------------------------------------------------------------------------------------------
        $sql = "SELECT sb.id FROM seo_base_route sb";
        $sql .= $clause;

        if (isset($searchFiltered->ordr) and Validate::not_null($searchFiltered->ordr)) {
            $dir = $searchFiltered->ordr['dir'];
            $columnNumber = $searchFiltered->ordr['column'];
            if (isset($columnNumber) and array_key_exists($columnNumber, $sortSQL)) {
                $sql .= " ORDER BY ".$sortSQL[$columnNumber]." $dir";
            }
        } else {
            $sql .= ' ORDER BY sb.id DESC';
        }


        if ($startLimit !== null and $endLimit !== null) {
            $sql .= " LIMIT ".$startLimit.", ".$endLimit;
        }

        $statement = $connection->prepare($sql);
        $filterResult = $statement->executeQuery()->fetchAllAssociative();
        $result = array();

        foreach ($filterResult as $key => $r) {
            $result[] = $this->find($r['id']);
        }

        //-----------------------------------------------------------------------------------------------------------------------
        return $result;
    }

}
