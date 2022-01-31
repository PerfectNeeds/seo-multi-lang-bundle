<?php

namespace PN\SeoBundle\Controller\Administration;

use Doctrine\ORM\EntityManagerInterface;
use PN\SeoBundle\Entity\SeoPage;
use PN\SeoBundle\Form\SeoPageType;
use PN\ServiceBundle\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * SeoPage controller.
 *
 * @Route("seo-page")
 */
class SeoPageController extends AbstractController
{

    /**
     * Lists all seoPage entities.
     *
     * @Route("/", name="seopage_index", methods={"GET"})
     */
    public function indexAction()
    {
        return $this->render('@PNSeo/Administration/SeoPage/index.html.twig');
    }

    /**
     * Creates a new seoPage entity.
     *
     * @Route("/new", name="seopage_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request, UserService $userService, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $seoPage = new Seopage();
        $form = $this->createForm(SeoPageType::class, $seoPage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userName = $userService->getUserName();
            $seoPage->setCreator($userName);
            $seoPage->setModifiedBy($userName);
            $em->persist($seoPage);
            $em->flush();

            $this->addFlash('success', 'Successfully saved');

            return $this->redirectToRoute('seopage_index');
        }

        return $this->render('@PNSeo/Administration/SeoPage/new.html.twig', array(
            'seoPage' => $seoPage,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing seoPage entity.
     *
     * @Route("/{id}/edit", name="seopage_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, SeoPage $seoPage, UserService $userService, EntityManagerInterface $em)
    {

        $editForm = $this->createForm(SeoPageType::class, $seoPage);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $userName = $userService->getUserName();
            $seoPage->setModifiedBy($userName);
            $em->flush();

            $this->addFlash('success', 'Successfully updated');

            return $this->redirectToRoute('seopage_edit', array('id' => $seoPage->getId()));
        }

        return $this->render('@PNSeo/Administration/SeoPage/edit.html.twig', array(
            'seoPage' => $seoPage,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a seoPage entity.
     *
     * @Route("/{id}", name="seopage_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, SeoPage $seoPage, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $em->remove($seoPage);
        $em->flush();

        return $this->redirectToRoute('seopage_index');
    }

    /**
     * Lists all seoPage entities.
     *
     * @Route("/data/table", defaults={"_format": "json"}, name="seopage_datatable", methods={"GET"})
     */
    public function dataTableAction(Request $request, EntityManagerInterface $em)
    {
        $srch = $request->query->all("search");
        $start = $request->query->getInt("start");
        $length = $request->query->getInt("length");
        $ordr = $request->query->all("order");

        $search = new \stdClass;
        $search->string = $srch['value'];
        $search->ordr = $ordr[0];

        $count = $em->getRepository(SeoPage::class)->filter($search, true);
        $seoPages = $em->getRepository(SeoPage::class)->filter($search, false, $start, $length);

        return $this->render('@PNSeo/Administration/SeoPage/datatable.json.twig', array(
                "recordsTotal" => $count,
                "recordsFiltered" => $count,
                "seoPages" => $seoPages,
            )
        );
    }

}
