<?php

namespace PN\SeoBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class VarsExtension extends AbstractExtension
{

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getBaseRoute', [VarsRuntime::class, 'getBaseRoute']),
            new TwigFunction('backlinks', [VarsRuntime::class, 'backlinks']),
        ];
    }

    public function getName(): string
    {
        return 'seo.twig.extension';
    }

}
