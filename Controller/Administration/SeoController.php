<?php

namespace PN\SeoBundle\Controller\Administration;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use PN\SeoBundle\Service\SeoFormTypeService;

/**
 * Seo controller.
 *
 * @Route("/")
 */
class SeoController extends Controller {

    protected $class = null;

    public function __construct(ContainerInterface $container) {
        $this->class = $container->getParameter("pn_seo_class");
    }

    /**
     * check that focusKeyword exist only one time
     *
     * @Route("/check-focus-keyword", name="fe_check_focus_keyword_ajax", methods={"GET"})
     */
    public function checkFocusKeyword(Request $request) {
        $seoId = $request->query->get('seoId');
        $focusKeyword = $request->query->get('focusKeyword');
        $em = $this->getDoctrine()->getManager();
        $return = 0;
        if ($seoId == NULL) {
            $seo = $em->getRepository($this->class)->findBy(array('focusKeyword' => $focusKeyword, 'deleted' => FALSE));
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
    public function checkSlug(Request $request) {
        $seoId = $request->query->get('seoId');
        $seoBaseRouteId = $request->query->get('seoBaseRouteId');
        $slug = $request->query->get('slug');
        $locale = $request->query->get('locale');
        $em = $this->getDoctrine()->getManager();
        $return = 0;

        $seoBaseRoute = $em->getRepository('PNSeoBundle:SeoBaseRoute')->find($seoBaseRouteId);
        if ($seoId == NULL) {
            $seo = new $this->class();
            $seo->setSlug($slug);
            $entity = $seo;
        } else {
            $seo = $em->getRepository($this->class)->find($seoId);
            $seo->setSlug($slug);
            $entity = $seo->getRelationalEntity();
        }

        $ifExist = $this->get(SeoFormTypeService::class)->checkSlugIfExist($seoBaseRoute, $entity, $seo, $locale);
        if ($ifExist == true) {
            $return = 1;
        } else {
            $return = 0;
        }
        return new Response($return);
    }

}
