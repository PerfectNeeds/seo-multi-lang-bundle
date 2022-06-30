<?php

namespace PN\SeoBundle\Service;

use PN\SeoBundle\Entity\Seo;
use PN\SeoBundle\Entity\SeoBaseRoute;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class SeoService
{

    protected $em;
    protected $context;
    protected $router;
    public $seoClass;
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->em = $container->get('doctrine.orm.entity_manager');
        $this->router = $container->get('router');
        $this->container = $container;
        $this->seoClass = $this->container->getParameter('pn_seo_class');
    }


    public function getRelationalEntity(Seo $seo)
    {
        $fullEntityName = null;
        $entityName = $seo->getSeoBaseRoute()->getEntityName();

        $convertedEntityName = $this->convertShortEntityNameToFullName($entityName);

        return $this->em->getRepository($convertedEntityName)->findOneBy(["seo" => $seo->getId()]);

    }

    private function checkSlugIfExist($entityClass, $slug, $locale = null)
    {
        $em = $this->em;

        $defaultLocale = $this->container->getParameter('locale');
        if ($locale == null) {
            $locale = $defaultLocale;
        }

        if ($locale == $defaultLocale) {
            return $em->getRepository($this->seoClass)->findOneBySlugAndEntity($slug, $entityClass);
        } else {
            return $em->getRepository($this->seoClass)->findOneSeoByLocale($entityClass, $slug, $locale);
        }
    }

    public function getSlug(Request $request, $slug, $entityClass, $slueRouteParamName = 'slug', $redirect = true)
    {
        if (!is_object($entityClass)) {
            throw new \Exception("Please enter a entity class");
        }
        $em = $this->em;
        $defaultLocale = $this->container->getParameter('locale');
        $locale = $request->getLocale();

        $seoEntityDefaultLocale = $this->checkSlugIfExist($entityClass, $slug, $defaultLocale);

        if ($seoEntityDefaultLocale) {
            $entity = $this->getRelationalEntity($seoEntityDefaultLocale);
            $slugLocale = $this->getSlugLocale($seoEntityDefaultLocale, $locale);
            if ($locale != $defaultLocale and $slugLocale != $slug and $redirect == true) {
                $newUrl = $this->changeLocaleInURL($request, $locale, $slugLocale, $slueRouteParamName);

                return new RedirectResponse($newUrl, 301);
            } else {
                return $entity;
            }
        }

        $seoEntityInAllLocale = $em->getRepository($this->seoClass)->findOneSeo($entityClass, $slug);
        if ($seoEntityInAllLocale) {
            $slugLocale = $this->getSlugLocale($seoEntityInAllLocale, $locale);
            if ($locale != $slugLocale and $slugLocale != $slug and $redirect == true) {
                $newUrl = $this->changeLocaleInURL($request, $locale, $slugLocale, $slueRouteParamName);

                return new RedirectResponse($newUrl, 301);
            } else {
                return $this->getRelationalEntity($seoEntityInAllLocale);
            }
        }

        return null;
    }

    private function getSlugLocale(Seo $seo, $locale)
    {
        foreach ($seo->getTranslations() as $translation) {
            if ($translation->getLanguage()->getLocale() == $locale) {
                return $translation->getSlug();
            }
        }

        return $seo->getSlug();
    }

    private function changeLocaleInURL(Request $request, $locale, $slug, $slueRouteParamName = 'slug')
    {
        $_route_params = $request->get('_route_params');
        if (!array_key_exists($slueRouteParamName, $_route_params)) {
            throw new \Exception('This parameter is wrong $slueRouteParamName. Please enter the slug name in route');
        }

        $allQuery = $request->query->all();
        $newParams = [
            "_locale" => $locale,
            $slueRouteParamName => $slug,
        ];

        $params = array_merge($_route_params, $newParams, $allQuery);

        return $this->router->generate($request->get('_route'), $params);
    }


    /**
     * convert ShortName to FullEntityName (Product => PN\Bundle\ProductBundle\Entity\Product)
     * @param $entityName
     * @return mixed|string
     * @throws \Exception
     */
    private function convertShortEntityNameToFullName($entityName)
    {
        if (substr_count($entityName, '\\') > 0) {
            return $entityName;
        }
        $fullEntityName = null;
        $meta = $this->em->getMetadataFactory()->getAllMetadata();
        foreach ($meta as $m) {
            if (array_key_exists("seo", $m->getAssociationMappings())) {
                if (substr($m->getName(), -1 * strlen($entityName) - 1) == '\\'.$entityName) {
                    $fullEntityName = $m->getName();
                }
            }


        }
        if ($fullEntityName != null) {
            $seoBaseRoute = $this->em->getRepository(SeoBaseRoute::class)->findOneBy(["entityName" => $entityName]);
            if ($seoBaseRoute instanceof SeoBaseRoute) {
                $seoBaseRoute->setEntityName($fullEntityName);
                $this->em->persist($seoBaseRoute);
                $this->em->flush();
            }

            return $fullEntityName;
        }
        throw new \Exception("Can't find FullEntityName '$entityName'");
    }
}
