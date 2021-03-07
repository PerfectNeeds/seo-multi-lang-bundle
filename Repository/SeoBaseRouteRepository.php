<?php

namespace PN\SeoBundle\Repository;

use Doctrine\ORM\EntityRepository;
use PN\ServiceBundle\Utils\SQL;
use PN\ServiceBundle\Utils\Validate;

class SeoBaseRouteRepository extends EntityRepository {

    public function findByEntity($entity, $error = true) {
        $entityName = (new \ReflectionClass($entity))->getShortName();
        $seoBaseRoute = $this->findOneBy(["entityName"=>$entityName]);

        if (!$seoBaseRoute AND $error == true) {
            throw new \Exception("Can't find SeoBaseRoute");
        }

        return $seoBaseRoute;
    }

    public function filter($search, $count = FALSE, $startLimit = NULL, $endLimit = NULL) {

        $sortSQL = [
            0 => 'sb.entity_name',
            1 => 'sb.base_route',
            2 => 'sb.created',
        ];
        $connection = $this->getEntityManager()->getConnection();
        $where = FALSE;
        $clause = '';

        $searchFiltered = new \stdClass();
        foreach ($search as $key => $value) {
            if (Validate::not_null($value) AND ! is_array($value)) {
                $searchFiltered->{$key} = substr($connection->quote($value), 1, -1);
            } else {
                $searchFiltered->{$key} = $value;
            }
        }


        if (isset($searchFiltered->string) AND $searchFiltered->string) {

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
            $statement->execute();
            return $queryResult = $statement->fetchColumn();
        }
//----------------------------------------------------------------------------------------------------------------------------------------------------
        $sql = "SELECT sb.id FROM seo_base_route sb";
        $sql .= $clause;

        if (isset($searchFiltered->ordr) AND Validate::not_null($searchFiltered->ordr)) {
            $dir = $searchFiltered->ordr['dir'];
            $columnNumber = $searchFiltered->ordr['column'];
            if (isset($columnNumber) AND array_key_exists($columnNumber, $sortSQL)) {
                $sql .= " ORDER BY " . $sortSQL[$columnNumber] . " $dir";
            }
        } else {
            $sql .= ' ORDER BY sb.id DESC';
        }


        if ($startLimit !== NULL AND $endLimit !== NULL) {
            $sql .= " LIMIT " . $startLimit . ", " . $endLimit;
        }

        $statement = $connection->prepare($sql);
        $statement->execute();
        $filterResult = $statement->fetchAll();
        $result = array();

        foreach ($filterResult as $key => $r) {
            $result[] = $this->find($r['id']);
        }
//-----------------------------------------------------------------------------------------------------------------------
        return $result;
    }

}
