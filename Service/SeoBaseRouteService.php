<?php

namespace PN\SeoBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use PN\SeoBundle\Entity\SeoBaseRoute;
use PN\SeoBundle\Repository\SeoBaseRouteRepository;
use PN\ServiceBundle\Utils\General;

class SeoBaseRouteService
{
    private EntityManagerInterface $em;
    private SeoBaseRouteRepository $seoBaseRouteRepository;

    public function __construct(EntityManagerInterface $em, SeoBaseRouteRepository $seoBaseRouteRepository)
    {
        $this->em = $em;
        $this->seoBaseRouteRepository = $seoBaseRouteRepository;
    }

    public function getBaseRoute($entity): SeoBaseRoute
    {
        if ($entity == null) {
            throw new Exception("Error: Seo Entity");
        }
        $seoBaseRoute = $this->seoBaseRouteRepository->findByEntity($entity, false);
        if (!$seoBaseRoute) {
            $entityName = (new \ReflectionClass($entity))->getShortName();
            $baseRoute = General::fromCamelCaseToUnderscore($entityName);

            $entityFullName = (new \ReflectionClass($entity))->getName();

            $seoBaseRoute = new SeoBaseRoute();
            $seoBaseRoute->setEntityName($entityFullName);
            $seoBaseRoute->setBaseRoute($baseRoute);
            $seoBaseRoute->setCreator("System by twig Extension");
            $seoBaseRoute->setModifiedBy("System by twig Extension");
            $this->em->persist($seoBaseRoute);
            $this->em->flush();
        }

        return $seoBaseRoute;
    }
}