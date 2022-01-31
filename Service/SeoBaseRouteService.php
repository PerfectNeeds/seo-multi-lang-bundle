<?php

namespace PN\SeoBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use PN\SeoBundle\Entity\SeoBaseRoute;
use PN\ServiceBundle\Utils\General;

class SeoBaseRouteService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getBaseRoute($entity): SeoBaseRoute
    {
        if ($entity == null) {
            throw new Exception("Error: Seo Entity");
        }
        $seoBaseRoute = $this->em->getRepository(SeoBaseRoute::class)->findByEntity($entity, false);
        if (!$seoBaseRoute) {
            $entityName = (new \ReflectionClass($entity))->getShortName();
            $baseRoute = General::fromCamelCaseToUnderscore($entityName);


            $seoBaseRoute = new SeoBaseRoute();
            $seoBaseRoute->setEntityName($entityName);
            $seoBaseRoute->setBaseRoute($baseRoute);
            $seoBaseRoute->setCreator("System by twig Extension");
            $seoBaseRoute->setModifiedBy("System by twig Extension");
            $this->em->persist($seoBaseRoute);
            $this->em->flush();
        }

        return $seoBaseRoute;
    }
}