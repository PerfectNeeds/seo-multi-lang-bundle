<?php

namespace PN\SeoBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

class PageNotFoundController extends AbstractController {

    public function pageNotFoundAction(Request $request, EntityManagerInterface $em) {
//        if ($this->container->getParameter('kernel.environment') != 'dev') {
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
