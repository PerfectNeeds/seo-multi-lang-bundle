<?php

namespace PN\SeoBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use PN\SeoBundle\Entity\Seo;
use PN\SeoBundle\Entity\Translation\SeoTranslation;
use PN\SeoBundle\Entity\SeoBaseRoute;
use PN\ServiceBundle\Utils\General;

class SeoFormTypeService {

    public $container;
    public $em;
    public $seoClass;
    public $defaultLocale;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->em = $container->get('doctrine.orm.entity_manager');
        $this->defaultLocale = $this->container->getParameter('locale');
        $this->seoClass = $this->container->getParameter('pn_seo_class');
    }

    public function checkAndGenerateSlug($entity, $seoEntity, $locale = null) {

        if (!$seoEntity instanceof Seo AND ! $seoEntity instanceof SeoTranslation) {
            throw new Exception('$seoEntity Must be instanceof Seo or SeoTranslation');
        }

        $em = $this->em;
        $seoBaseRoute = $em->getRepository('PNSeoBundle:SeoBaseRoute')->findByEntity($entity);

        if ($locale == null) {
            $locale = $this->defaultLocale;
        }

        if ($seoEntity->getSlug() == null) {
            $slug = $this->getSlug($entity, $seoEntity, $locale);
            $seoEntity->setSlug($slug);
        }
        $slugIfExist = $this->checkSlugIfExist($seoBaseRoute, $entity, $seoEntity, $locale);
        if ($slugIfExist) {
            return $this->generateSlug($seoBaseRoute, $entity, $seoEntity, $locale);
        }
        return $seoEntity->getSlug();
    }

    public function checkSlugIfExist(SeoBaseRoute $seoBaseRoute, $entity, $seoEntity, $locale) {
        if ($locale == $this->defaultLocale) {
            return $this->checkSlugIfExistInDefaultLocale($seoBaseRoute, $entity, $seoEntity);
        } else {
            return $this->checkSlugIfExistInTrans($seoBaseRoute, $entity, $seoEntity, $locale);
        }
    }

    private function checkSlugIfExistInDefaultLocale(SeoBaseRoute $seoBaseRoute, $entity, $seoEntity) {
        $em = $this->em;
        $slug = $this->getSlug($entity, $seoEntity, $this->defaultLocale);
        if (!method_exists($entity, "getSeo") OR $entity->getSeo() == null OR $entity->getSeo()->getId() == null) { // new
            $checkSeo = $em->getRepository($this->seoClass)->findOneBy(array('seoBaseRoute' => $seoBaseRoute->getId(), 'slug' => $slug, 'deleted' => FALSE));
        } else { // edit
            $checkSeo = $em->getRepository($this->seoClass)->findBySlugAndBaseRouteAndNotId($seoBaseRoute->getId(), $slug, $entity->getSeo()->getId());
        }

        if ($checkSeo != null) {
            return true;
        }
        return false;
    }

    private function checkSlugIfExistInTrans(SeoBaseRoute $seoBaseRoute, $entity, $seoEntity, $locale) {
        $em = $this->em;
        $slug = $this->getSlug($entity, $seoEntity, $locale);

        if (!method_exists($entity, "getId") OR $entity->getId() == null) { // new
            $checkSeo = $em->getRepository($this->seoClass)->findTranBySlugAndBaseRoute($seoBaseRoute->getId(), $slug, $locale);
        } else { // edit
            $checkSeo = $em->getRepository($this->seoClass)->findTranBySlugAndBaseRouteAndNotId($seoBaseRoute->getId(), $slug, $entity->getSeo()->getId(), $locale);
        }
        if ($checkSeo != null) {
            return true;
        }
        return false;
    }

    //DONE
    private function generateSlug(SeoBaseRoute $seoBaseRoute, $entity, $seoEntity, $locale) {
        $tempSlug = $this->getSlug($entity, $seoEntity, $locale);
        $i = 0;
        do {
            if ($i == 0) {
                $slug = General::seoUrl($tempSlug);
            } else {
                $slug = General::seoUrl($tempSlug . '-' . $i);
            }
            $seoEntity->setSlug($slug);
            $slugIfExist = $this->checkSlugIfExist($seoBaseRoute, $entity, $seoEntity, $locale);
            $i++;
        } while ($slugIfExist == true);
        return $slug;
    }

    //DONE
    private function getSlug($entity, $seoEntity, $locale) {
        if ($seoEntity->getSlug()) {
            return $seoEntity->getSlug();
        } else {
            $title = $this->getTitle($entity, $locale);
            return General::seoUrl($title);
        }
        return null;
    }

    //DONE
    private function getTitle($entity, $locale) {
        $title = null;
        if ($locale == $this->defaultLocale) {
            $title = $this->getEntityTitle($entity);
        } elseif (method_exists($entity, "getTranslations")) {
            foreach ($entity->getTranslations() as $translation) {
                if ($translation->getLanguage()->getLocale() == $locale) {
                    $title = $this->getEntityTitle($translation);
                }
            }
        }

        if ($title == null) {
            $title = General::generateRandString();
        }

        return $title;
    }

    //DONE
    private function getEntityTitle($entity) {
        if (method_exists($entity, "getTitle")) {
            return $entity->getTitle();
        } elseif (method_exists($entity, "getName")) {
            return $entity->getName();
        } elseif (method_exists($entity, "__toString")) {
            return $entity->__toString();
        }
        return null;
    }

}
