<?php

namespace PN\SeoBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use PN\SeoBundle\Entity\SeoBaseRoute;
use Twig\Extension\RuntimeExtensionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use PN\ServiceBundle\Utils\General;

class VarsRuntime implements RuntimeExtensionInterface
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getBaseRoute($entity)
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

    public function backlinks($str)
    {
        if (strlen($str) == 0) {
            return $str;
        }

        $backLinks = $this->em->getRepository('PNSeoBundle:BackLink')->findAllByJSON();

        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML(mb_convert_encoding($str, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new \DOMXPath($doc);
        $text_nodes = $xpath->evaluate('//text()');
        $searchArr = array();
        $replaceArr = array();
        foreach ($backLinks as $backLink) {
            $searchArr[] = $backLink['word'];
            $replaceArr[] = '<a href="'.$backLink['link'].'" target="_blank" rel="dofollow">'.$backLink['word'].'</a>';
        }

        foreach ($text_nodes as $text_node) {
            $text_node->nodeValue = str_replace($searchArr, $replaceArr, $text_node->nodeValue);
        }

        return html_entity_decode($doc->saveHTML());
    }

}
