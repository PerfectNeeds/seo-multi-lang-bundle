<?php

namespace PN\SeoBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use PN\SeoBundle\Entity\SeoBaseRoute;
use PN\SeoBundle\Entity\Seo;

class SeoService {

    protected $em;
    protected $context;
    protected $router;
    public $seoClass;
    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->em = $container->get('doctrine.orm.entity_manager');
        $this->router = $container->get('router');
        $this->container = $container;
        $this->seoClass = $this->container->getParameter('pn_seo_class');
    }

    private function checkSlugIfExist(SeoBaseRoute $seoBaseRoute, $slug, $locale = null) {
        $em = $this->em;

        $defaultLocale = $this->container->getParameter('locale');
        if ($locale == null) {
            $locale = $defaultLocale;
        }
        if ($locale == $defaultLocale) {
            return $em->getRepository($this->seoClass)->findOneBy(['slug' => $slug, 'seoBaseRoute' => $seoBaseRoute->getId()]);
        } else {
            return $em->getRepository($this->seoClass)->findOneSeoByLocale($seoBaseRoute->getId(), $slug, $locale);
        }
    }

    public function getSlug(Request $request, $slug, $entityClass, $slueRouteParamName = 'slug', $redirect = true) {
        if (!is_object($entityClass)) {
            throw new \Exception("Please enter a entity class");
        }
        $em = $this->em;
        $seoBaseRoute = $em->getRepository('PNSeoBundle:SeoBaseRoute')->findByEntity($entityClass);

        $defaultLocale = $this->container->getParameter('locale');
        $locale = $request->getLocale();

        $seoEntityDefaultLocale = $this->checkSlugIfExist($seoBaseRoute, $slug, $defaultLocale);
        if ($seoEntityDefaultLocale) {
            $entity = $seoEntityDefaultLocale->getRelationalEntity();
            $slugLocale = $this->getSlugLocale($seoEntityDefaultLocale, $locale);
            if ($locale != $defaultLocale and $slugLocale != $slug and $redirect == true) {
                $newUrl = $this->changeLocaleInURL($request, $locale, $slugLocale, $slueRouteParamName);
                return new RedirectResponse($newUrl, 301);
            } else {
                return $entity;
            }
        }

        $seoEntityInAllLocale = $em->getRepository($this->seoClass)->findOneSeo($seoBaseRoute->getId(), $slug);
        if ($seoEntityInAllLocale) {
            $slugLocale = $this->getSlugLocale($seoEntityInAllLocale, $locale);
            if ($locale != $slugLocale and $slugLocale != $slug and $redirect == true) {
                $newUrl = $this->changeLocaleInURL($request, $locale, $slugLocale, $slueRouteParamName);
                return new RedirectResponse($newUrl, 301);
            } else {
                return $seoEntityInAllLocale->getRelationalEntity();
            }
        }

        return null;
    }

    private function getSlugLocale(Seo $seo, $locale) {
        foreach ($seo->getTranslations() as $translation) {
            if ($translation->getLanguage()->getLocale() == $locale) {
                return $translation->getSlug();
            }
        }
        return $seo->getSlug();
    }

    private function changeLocaleInURL(Request $request, $locale, $slug, $slueRouteParamName = 'slug') {
        $_route_params = $request->get('_route_params');
        if (!array_key_exists($slueRouteParamName, $_route_params)) {
            throw new \Exception('This parameter is wrong $slueRouteParamName. Please enter the slug name in route');
        }

        $allQuery = $request->query->all();
        $newParams = [
            "_locale" => $locale,
            $slueRouteParamName => $slug
        ];

        $params = array_merge($_route_params, $newParams, $allQuery);
        return $this->router->generate($request->get('_route'), $params);
    }

}
