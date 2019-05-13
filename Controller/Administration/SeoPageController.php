<?php

namespace PN\SeoBundle\Controller\Administration;

use PN\SeoBundle\Entity\SeoPage;
use PN\SeoBundle\Form\SeoPageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * SeoPage controller.
 *
 * @Route("seo-page")
 */
class SeoPageController extends Controller {

    /**
     * Lists all seoPage entities.
     *
     * @Route("/", name="seopage_index", methods={"GET"})
     */
    public function indexAction() {
        return $this->render('PNSeoBundle:Administration/SeoPage:index.html.twig');
    }

    /**
     * Creates a new seoPage entity.
     *
     * @Route("/new", name="seopage_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request) {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $seoPage = new Seopage();
        $form = $this->createForm(SeoPageType::class, $seoPage);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $userName = $this->get('user')->getUserName();
            $seoPage->setCreator($userName);
            $seoPage->setModifiedBy($userName);
            $em->persist($seoPage);
            $em->flush();

            $this->addFlash('success', 'Successfully saved');

            return $this->redirectToRoute('seopage_index');
        }

        return $this->render('PNSeoBundle:Administration/SeoPage:new.html.twig', array(
                    'seoPage' => $seoPage,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing seoPage entity.
     *
     * @Route("/{id}/edit", name="seopage_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, SeoPage $seoPage) {

        $editForm = $this->createForm(SeoPageType::class, $seoPage);
        $editForm->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $userName = $this->get('user')->getUserName();
            $seoPage->setModifiedBy($userName);
            $em->flush();

            $this->addFlash('success', 'Successfully updated');

            return $this->redirectToRoute('seopage_edit', array('id' => $seoPage->getId()));
        }

        return $this->render('PNSeoBundle:Administration/SeoPage:edit.html.twig', array(
                    'seoPage' => $seoPage,
                    'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a seoPage entity.
     *
     * @Route("/{id}", name="seopage_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, SeoPage $seoPage) {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $em = $this->getDoctrine()->getManager();
        $em->remove($seoPage);
        $em->flush();

        return $this->redirectToRoute('seopage_index');
    }

    /**
     * Lists all seoPage entities.
     *
     * @Route("/data/table", defaults={"_format": "json"}, name="seopage_datatable", methods={"GET"})
     */
    public function dataTableAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $srch = $request->query->get("search");
        $start = $request->query->get("start");
        $length = $request->query->get("length");
        $ordr = $request->query->get("order");

        $search = new \stdClass;
        $search->string = $srch['value'];
        $search->ordr = $ordr[0];

        $count = $em->getRepository('PNSeoBundle:SeoPage')->filter($search, TRUE);
        $seoPages = $em->getRepository('PNSeoBundle:SeoPage')->filter($search, FALSE, $start, $length);

        return $this->render('PNSeoBundle:Administration/SeoPage:datatable.json.twig', array(
                    "recordsTotal" => $count,
                    "recordsFiltered" => $count,
                    "seoPages" => $seoPages,
                        )
        );
    }

}
