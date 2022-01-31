<?php

namespace PN\SeoBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use PN\SeoBundle\Service\SeoBaseRouteService;
use Twig\Extension\RuntimeExtensionInterface;

class VarsRuntime implements RuntimeExtensionInterface
{

    private EntityManagerInterface $em;
    private SeoBaseRouteService $baseRouteService;

    public function __construct(EntityManagerInterface $em, SeoBaseRouteService $baseRouteService)
    {
        $this->em = $em;
        $this->baseRouteService = $baseRouteService;
    }

    public function getBaseRoute($entity)
    {
        return $this->baseRouteService->getBaseRoute($entity);
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
