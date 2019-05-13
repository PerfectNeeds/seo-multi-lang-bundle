<?php

namespace PN\SeoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

class PageNotFoundController extends Controller {

    public function pageNotFoundAction(Request $request) {
//        if ($this->container->getParameter('kernel.environment') != 'dev') {
//            $em = $this->getDoctrine()->getManager();
//
//            $currentUrl = $request->getUri();
//            $entity = $em->getRepository('PNSeoBundle:Redirect404')->findOneBy(["from" => $currentUrl]);
//            if ($entity) {
//                return $this->redirect($entity->getTo());
//            }
//        }
        throw $this->createNotFoundException();
        return [];
    }

}
