<?php

namespace PN\SeoBundle\Controller\Administration;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use PN\SeoBundle\Entity\Redirect404;
use PN\SeoBundle\Form\Redirect404Type;

/**
 * Redirect404 controller.
 *
 * @Route("/redirect-404")
 */
class Redirect404Controller extends Controller {

    /**
     * Lists all Redirect404 entities.
     *
     * @Route("/", name="redirect404_index", methods={"GET"})
     */
    public function indexAction() {

        return $this->render('@PNSeo/Administration/Redirect404/index.html.twig');
    }

    /**
     * Creates a new Redirect404 entity.
     *
     * @Route("/new", name="redirect404_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request) {
        $redirect404 = new Redirect404();
        $form = $this->createForm(Redirect404Type::class, $redirect404);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {

            $userName = $this->get('user')->getUserName();
            $redirect404->setCreator($userName);
            $redirect404->setModifiedBy($userName);
            $em->persist($redirect404);
            $em->flush();

            $this->addFlash('success', 'Successfully saved');

            return $this->redirectToRoute('redirect404_index');
        }

        return $this->render('@PNSeo/Administration/Redirect404/new.html.twig', array(
                    'redirect404' => $redirect404,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Redirect404 entity.
     *
     * @Route("/{id}/edit", name="redirect404_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, Redirect404 $redirect404) {

        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $editForm = $this->createForm(Redirect404Type::class, $redirect404);
        $editForm->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $userName = $this->get('user')->getUserName();
            $redirect404->setModifiedBy($userName);
            $em->flush();

            $this->addFlash('success', 'Successfully updated');

            return $this->redirectToRoute('redirect404_edit', array('id' => $redirect404->getId()));
        }


        return $this->render('@PNSeo/Administration/Redirect404/edit.html.twig', array(
                    'redirect404' => $redirect404,
                    'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Redirect404 entity.
     *
     * @Route("/{id}", name="redirect404_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, Redirect404 $redirect404) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($redirect404);
        $em->flush();

        return $this->redirectToRoute('redirect404_index');
    }

    /**
     * Lists all seoPage entities.
     *
     * @Route("/data/table", defaults={"_format": "json"}, name="redirect404_datatable", methods={"GET"})
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

        $count = $em->getRepository('PNSeoBundle:Redirect404')->filter($search, TRUE);
        $redirect404s = $em->getRepository('PNSeoBundle:Redirect404')->filter($search, FALSE, $start, $length);

        return $this->render('@PNSeo/Administration/Redirect404/datatable.json.twig', array(
                    "recordsTotal" => $count,
                    "recordsFiltered" => $count,
                    "redirect404s" => $redirect404s,
                        )
        );
    }

}
