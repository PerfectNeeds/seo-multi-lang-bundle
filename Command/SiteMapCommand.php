<?php

namespace PN\SeoBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use PN\ServiceBundle\Lib\UploadPath;
use PN\SeoBundle\Lib\Sitemap;

class SiteMapCommand extends ContainerAwareCommand {

    private $locales = [];
    private $seoClass;

    protected function configure() {
        $this
                ->setName('sitemap')
                ->setDescription("Generate new sitemap")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $this->seoClass = $this->getContainer()->getParameter('pn_seo_class');

        $ignoreRouteNames = [
            '_wdt',
            '_twig',
            '_profiler',
            'fos_',
            'login',
            'admin/',
            'ajax',
            'menu',
            'test',
        ];
        $ignoreRoutePath = [
            '{',
            'admin',
            'ajax',
            'test',
            'menu',
            'footer',
        ];

        $sitemapRoutes = [];
        $router = $this->getContainer()->get('router');
        $routes = $router->getRouteCollection();
        foreach ($routes as $routeName => $route) {
            if (!$this->strposa($routeName, $ignoreRouteNames)) {
                if (in_array('GET', $route->getMethods())) {
                    $path = $route->getPath();
                    if (strpos($path, '{_locale}') !== FALSE) {
                        $path = str_replace('{_locale}', '', $path);
                    }
                    if (!$this->strposa($path, $ignoreRoutePath)) {
                        $this->getLocal($route);
                        $sitemapRoutes[] = $route;
                    }
                }
            }
        }

        $webDir = UploadPath::getRootDir();
        if (!file_exists($webDir . 'sitemap.xsl')) {
            $src = __DIR__ . '/../Resources/views/sitemap.xsl';
            copy($src, $webDir . 'sitemap.xsl');
        }

        $sitemap = new Sitemap('http://localhost/OrientalTours/web');
        $sitemap->setPath($webDir);
        $sitemap->setFilename('sitemap');
        $sitemap->addItem('/', '1.0', 'daily', 'Today');

        //add links to site map file
        foreach ($sitemapRoutes as $sitemapRoute) {
            $sitemapPath = $sitemapRoute->getPath();
            if (count($this->locales) > 0) {
                foreach ($this->locales as $locale) {
                    //add Item
                    $sitemap->addItem(str_replace('{_locale}', $locale, $sitemapPath), '0.8', 'monthly', 'Today');
                }
            } else {
                $sitemap->addItem($sitemapPath, '0.8', 'monthly', 'Today');
            }
        }
        $entities = $em->getRepository($this->seoClass)->findBy(['deleted' => FALSE]);
        foreach ($entities as $entity) {
            if ($entity->getSeoBaseRoute() != NULL) {
                $loc = '/' . $entity->getSeoBaseRoute()->getBaseRoute() . '/' . $entity->getslug();
                $lastModified = $entity->getLastModified()->format('c');
                $sitemap->addItem($loc, '5.0', 'daily', $lastModified);
            }
        }
        $sitemap->createSitemapIndex('http://example.com/sitemap/', 'Today');
    }

    private function strposa($haystack, $needle, $offset = 0) {
        if (!is_array($needle)) {
            $needle = array($needle);
        }
        foreach ($needle as $query) {
            if (strpos($haystack, $query, $offset) !== false) {
                return true; // stop on first true result
            }
        }
        return false;
    }

    private function getLocal(\Symfony\Component\Routing\Route $route) {
        $routeRequirements = $route->getRequirements();
        if (array_key_exists('_locale', $routeRequirements)) {
            $locales = explode('|', $routeRequirements['_locale']);
            if (count($locales) > 2) {
                unset($locales[0]);
                foreach ($locales as $locale) {
                    if (!in_array($locale, $this->locales)) {
                        $this->locales[] = $locale;
                    }
                }
            }
        }
    }

}
