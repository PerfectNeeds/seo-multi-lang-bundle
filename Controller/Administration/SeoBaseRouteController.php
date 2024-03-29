<?php

namespace PN\SeoBundle\Controller\Administration;

use PN\SeoBundle\Entity\SeoBaseRoute;
use PN\SeoBundle\Form\SeoBaseRouteType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Seobaseroute controller.
 *
 * @Route("base-route")
 */
class SeoBaseRouteController extends Controller
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
    public function newAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $entities = $this->getEntitiesHasSeoEntity();
        $seoBaseRoute = new SeoBaseRoute();
        $form = $this->createForm(SeoBaseRouteType::class, $seoBaseRoute, ["entitiesNames" => $entities]);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $userName = $this->get('user')->getUserName();
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
    public function editAction(Request $request, SeoBaseRoute $seoBaseRoute)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');


        $entities = $this->getEntitiesHasSeoEntity();
        $editForm = $this->createForm(SeoBaseRouteType::class, $seoBaseRoute, ["entitiesNames" => $entities]);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $userName = $this->get('user')->getUserName();
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
    public function dataTableAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $srch = $request->query->get("search");
        $start = $request->query->get("start");
        $length = $request->query->get("length");
        $ordr = $request->query->get("order");

        $search = new \stdClass;
        $search->string = $srch['value'];
        $search->ordr = $ordr[0];

        $count = $em->getRepository('PNSeoBundle:SeoBaseRoute')->filter($search, true);
        $seoBaseRoutes = $em->getRepository('PNSeoBundle:SeoBaseRoute')->filter($search, false, $start, $length);
        foreach ($seoBaseRoutes as $seoBaseRoute) {
            $fullEntityName = $this->convertShortEntityNameToFullName($seoBaseRoute->getEntityName());
            if ($fullEntityName !== null) {
                $seoBaseRoute->setEntityName($fullEntityName);
                $em->persist($seoBaseRoute);
                $em->flush();
            }
        }

        return $this->render("@PNSeo/Administration/SeoBaseRoute/datatable.json.twig", [
                "recordsTotal" => $count,
                "recordsFiltered" => $count,
                "seoBaseRoutes" => $seoBaseRoutes,
            ]
        );
    }

    private function getEntitiesHasSeoEntity()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = [];
        $meta = $em->getMetadataFactory()->getAllMetadata();
        foreach ($meta as $m) {
            if (array_key_exists("seo", $m->getAssociationMappings())) {
                $entityNames = (new \ReflectionClass($m->getName()))->getName();
                if (!in_array($entityNames, ["SeoSocial"])) {
                    $entities[$entityNames] = $entityNames;
                }
            }
        }

        return $entities;
    }

    private function convertShortEntityNameToFullName($entityName)
    {
        if (substr_count($entityName, '\\') > 0) {
            return null;
        }
        $fullEntityName = null;

        $em = $this->getDoctrine()->getManager();
        $meta = $em->getMetadataFactory()->getAllMetadata();
        foreach ($meta as $m) {
            if (array_key_exists("seo", $m->getAssociationMappings())) {
                if (substr($m->getName(), -1 * strlen($entityName) - 1) == '\\'.$entityName) {
                    $fullEntityName = $m->getName();
                }
            }


        }
        if ($fullEntityName != null) {
            return $fullEntityName;
        }

        return null;
    }

}
