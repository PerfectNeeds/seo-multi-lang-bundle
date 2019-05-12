<?php

namespace PN\SeoBundle\Twig;

use Twig\Extension\AbstractExtension;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\TwigFunction;
use PN\SeoBundle\Twig\VarsRuntime;

class VarsExtension extends AbstractExtension {

    public function getFunctions() {
        return array(
            new TwigFunction('getBaseRoute', array(VarsRuntime::class, 'getBaseRoute')),
            new TwigFunction('backlinks', array(VarsRuntime::class, 'backlinks')),
        );
    }

    public function getName() {
        return 'seo.twig.extension';
    }

}
