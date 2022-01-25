<?php

namespace PN\SeoBundle\Controller\Administration;

use Doctrine\ORM\EntityManagerInterface;
use PN\SeoBundle\Form\SeoBaseRouteType;
use PN\ServiceBundle\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use PN\SeoBundle\Entity\SeoBaseRoute;
use Symfony\Component\HttpFoundation\Request;
use PN\ServiceBundle\Service\CommonFunctionService;

/**
 * Seobaseroute controller.
 *
 * @Route("base-route")
 */
class SeoBaseRouteController extends AbstractController
{

    /**
     * Lists all seoBaseRoute entities.
     *
     * @Route("/", name="seobaseroute_index", methods={"GET"})
     */
    public function indexAction()
    {
        return $this->render('@PNSeo/Administration/SeoBaseRoute/index.html.twig');
    }

    /**
     * Creates a new seoBaseRoute entity.
     *
     * @Route("/new", name="seobaseroute_new", methods={"GET", "POST"})
     */
    public function newAction(
        Request $request,
        CommonFunctionService $commonFunctionService,
        UserService $userService,
        EntityManagerInterface $em
    ) {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $entities = $this->getEntitiesHasSeoEntity($commonFunctionService);
        $seoBaseRoute = new SeoBaseRoute();
        $form = $this->createForm(SeoBaseRouteType::class, $seoBaseRoute, ["entitiesNames" => $entities]);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $userName = $userService->getUserName();
            $seoBaseRoute->setCreator($userName);
            $seoBaseRoute->setModifiedBy($userName);
            $em->persist($seoBaseRoute);
            $em->flush();

            $this->addFlash('success', 'Successfully saved');

            return $this->redirectToRoute('seobaseroute_index');
        }

        return $this->render('@PNSeo/Administration/SeoBaseRoute/new.html.twig', [
                'seoBaseRoute' => $seoBaseRoute,
                'form' => $form->createView(),
                'entities' => $entities,
            ]
        );
    }

    /**
     * Displays a form to edit an existing seoBaseRoute entity.
     *
     * @Route("/{id}/edit", name="seobaseroute_edit", methods={"GET", "POST"})
     */
    public function editAction(
        Request $request,
        SeoBaseRoute $seoBaseRoute,
        CommonFunctionService $commonFunctionService,
        UserService $userService,
        EntityManagerInterface $em
    ) {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $entities = $this->getEntitiesHasSeoEntity($commonFunctionService);
        $editForm = $this->createForm(SeoBaseRouteType::class, $seoBaseRoute, ["entitiesNames" => $entities]);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $userName = $userService->getUserName();
            $seoBaseRoute->setModifiedBy($userName);
            $em->flush();

            $this->addFlash('success', 'Successfully updated');

            return $this->redirectToRoute('seobaseroute_edit', array('id' => $seoBaseRoute->getId()));
        }

        return $this->render('@PNSeo/Administration/SeoBaseRoute/edit.html.twig', [
                'seoBaseRoute' => $seoBaseRoute,
                'edit_form' => $editForm->createView(),
            ]
        );
    }

    /**
     * Lists all seoBaseRoute entities.
     *
     * @Route("/data/table", defaults={"_format": "json"}, name="seobaseroute_datatable", methods={"GET"})
     */
    public function dataTableAction(Request $request, EntityManagerInterface $em)
    {
        $srch = $request->query->get("search");
        $start = $request->query->get("start");
        $length = $request->query->get("length");
        $ordr = $request->query->get("order");

        $search = new \stdClass;
        $search->string = $srch['value'];
        $search->ordr = $ordr[0];

        $count = $em->getRepository('PNSeoBundle:SeoBaseRoute')->filter($search, true);
        $seoBaseRoutes = $em->getRepository('PNSeoBundle:SeoBaseRoute')->filter($search, false, $start, $length);

        return $this->render("@PNSeo/Administration/SeoBaseRoute/datatable.json.twig", [
                "recordsTotal" => $count,
                "recordsFiltered" => $count,
                "seoBaseRoutes" => $seoBaseRoutes,
            ]
        );
    }

    private function getEntitiesHasSeoEntity(CommonFunctionService $commonFunctionService)
    {
        return $commonFunctionService->getEntitiesWithObject('seo', ["SeoSocial"]);
    }

}
