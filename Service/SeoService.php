<?php

namespace PNSeoBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\Container;
use PNSeoBundle\Entity\SeoBaseRoute;
use PNSeoBundle\Entity\Seo;
use PN\Utils\General;

class SeoService {

    protected $entityManager;
    protected $context;
    protected $router;
    protected $container;

    public function __construct($entityManager, Router $router, Container $container) {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->container = $container;
    }

    private function checkSlugIfExist(SeoBaseRoute $seoBaseRoute, $slug, $locale = null) {
        $em = $this->entityManager;

        $defaultLocale = $this->container->getParameter('locale');
        if ($locale == null) {
            $locale = $defaultLocale;
        }
        if ($locale == $defaultLocale) {
            return $em->getRepository('SeoBundle:Seo')->findOneBy(['slug' => $slug, 'seoBaseRoute' => $seoBaseRoute->getId()]);
        } else {
            return $em->getRepository('SeoBundle:Seo')->findOneSeoByLocale($seoBaseRoute->getId(), $slug, $locale);
        }
    }

    public function getSlug(Request $request, $slug, $entityClass) {
        if (!is_object($entityClass)) {
            throw new \Exception("Please enter a entity class");
        }
        $em = $this->entityManager;
        $seoBaseRoute = $em->getRepository('SeoBundle:SeoBaseRoute')->findByEntity($entityClass);

        $defaultLocale = $this->container->getParameter('locale');
        $locale = $request->getLocale();

        $seoEntityDefaultLocale = $this->checkSlugIfExist($seoBaseRoute, $slug, $defaultLocale);
        if ($seoEntityDefaultLocale) {
            $entity = $seoEntityDefaultLocale->getRelationalEntity();
            if ($locale != $defaultLocale) {
                $newUrl = $this->changeLocaleInURL($request, $defaultLocale);
                return new RedirectResponse($newUrl, 301);
            } else {
                return $entity;
            }
        }

        $seoEntityInAllLocale = $em->getRepository('SeoBundle:Seo')->findOneSeo($seoBaseRoute->getId(), $slug);
        if ($seoEntityInAllLocale) {
            $slugLocale = $this->getSlugLocale($seoEntityInAllLocale, $slug);
            if ($locale != $slugLocale) {
                $newUrl = $this->changeLocaleInURL($request, $slugLocale);
                return new RedirectResponse($newUrl, 301);
            } else {
                return $seoEntityInAllLocale->getRelationalEntity();
            }
        }

        return null;
    }

    private function getSlugLocale(Seo $seo, $slug) {
        foreach ($seo->getTranslations() as $translation) {
            if ($translation->getSlug() == $slug) {
                return $translation->getLanguage()->getLocale();
            }
        }
        return $this->container->getParameter('locale');
    }

    private function changeLocaleInURL(Request $request, $locale) {
        $params = array_merge($request->get('_route_params'), ["_locale" => $locale]);
        return $this->router->generate($request->get('_route'), $params);
    }

}
