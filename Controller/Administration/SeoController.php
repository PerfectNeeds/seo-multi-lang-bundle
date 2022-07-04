<?php

namespace PN\SeoBundle\Controller\Administration;

use Doctrine\ORM\EntityManagerInterface;
use PN\SeoBundle\Entity\SeoBaseRoute;
use PN\SeoBundle\Service\SeoFormTypeService;
use PN\SeoBundle\Service\SeoService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Seo controller.
 *
 * @Route("/")
 */
class SeoController extends AbstractController
{

    private $class = null;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->class = $parameterBag->get("pn_seo_class");
    }

    /**
     * check that focusKeyword exist only one time
     *
     * @Route("/check-focus-keyword", name="fe_check_focus_keyword_ajax", methods={"GET"})
     */
    public function checkFocusKeyword(Request $request, EntityManagerInterface $em)
    {
        $seoId = $request->query->get('seoId');
        $focusKeyword = $request->query->get('focusKeyword');
        $return = 0;
        if ($seoId == null) {
            $seo = $em->getRepository($this->class)->findBy(array('focusKeyword' => $focusKeyword, 'deleted' => false));
            if (count($seo) > 0) {
                $return = count($seo);
            }
        } else {
            $seo = $em->getRepository($this->class)->findByFocusKeywordAndNotId($focusKeyword, $seoId);
            if (count($seo) > 0) {
                $return = count($seo);
            }
        }

        return new Response($return);
    }

    /**
     * check that Slug exist only one time
     *
     * @Route("/check-slug", name="fe_check_slug_ajax", methods={"GET"})
     */
    public function checkSlug(Request $request, SeoFormTypeService $seoFormTypeService, SeoService $seoService, EntityManagerInterface $em)
    {
        $seoId = $request->query->get('seoId');
        $seoBaseRouteId = $request->query->get('seoBaseRouteId');
        $slug = $request->query->get('slug');
        $locale = $request->query->get('locale');
        $return = 0;

        $seoBaseRoute = $em->getRepository(SeoBaseRoute::class)->find($seoBaseRouteId);
        if ($seoId == null) {
            $seo = new $this->class();
            $seo->setSlug($slug);
            $entity = $seo;
        } else {
            $seo = $em->getRepository($this->class)->find($seoId);
            $seo->setSlug($slug);
            $entity = $seoService->getRelationalEntity($seo);
        }

        $ifExist = $seoFormTypeService->checkSlugIfExist($seoBaseRoute, $entity, $seo, $locale);
        if ($ifExist == true) {
            $return = 1;
        } else {
            $return = 0;
        }

        return new Response($return);
    }

}
