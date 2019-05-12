<?php

namespace PN\SeoBundle\Controller\Administration;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use PN\SeoBundle\Entity\Seo;

/**
 * Seo controller.
 *
 * @Route("/")
 */
class SeoController extends Controller {

    protected $em = null;

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
            $seo = $em->getRepository('SeoBundle:Seo')->findBy(array('focusKeyword' => $focusKeyword, 'deleted' => FALSE));
            if (count($seo) > 0) {
                $return = count($seo);
            }
        } else {
            $seo = $em->getRepository('SeoBundle:Seo')->findByFocusKeywordAndNotId($focusKeyword, $seoId);
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

        $seoBaseRoute = $em->getRepository('SeoBundle:SeoBaseRoute')->find($seoBaseRouteId);
        if ($seoId == NULL) {
            $seo = new Seo();
            $seo->setSlug($slug);
            $entity = $seo;
        } else {
            $seo = $em->getRepository('SeoBundle:Seo')->find($seoId);
            $seo->setSlug($slug);
            $entity = $seo->getRelationalEntity();
        }

        $ifExist = $this->get('seo_form_type')->checkSlugIfExist($seoBaseRoute, $entity, $seo, $locale);
        if ($ifExist == true) {
            $return = 1;
        } else {
            $return = 0;
        }
        return new Response($return);

        if ($seoId == NULL) {
            $checkSeo = $em->getRepository('SeoBundle:Seo')->findOneBy(array('seoBaseRoute' => $seoBaseRoute->getId(), 'slug' => $slug, 'deleted' => FALSE));
            if ($checkSeo != null) {
                $return = 1;
            }
        } else {
            $checkSeo = $em->getRepository('SeoBundle:Seo')->findBySlugAndBaseRouteAndNotId($seoBaseRoute->getId(), $slug, $seoId);
            if ($checkSeo != null) {
                $return = 1;
            }
        }
    }

}
