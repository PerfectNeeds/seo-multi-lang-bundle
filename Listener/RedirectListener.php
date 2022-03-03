<?php

namespace PN\SeoBundle\Listener;

use Doctrine\ORM\EntityManagerInterface;
use PN\SeoBundle\Entity\Redirect404;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class RedirectListener
{

    private EntityManagerInterface $em;


    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if ($event->isMainRequest()) {
            $url = $event->getRequest()->getUri();
            if (strpos($url, "/admin/") === false) {
                $redirectPage = $this->em->getRepository(Redirect404::class)->findOneBy(["from" => $url]);
                if ($redirectPage) {
                    $event->setResponse(new RedirectResponse($redirectPage->getTo()));
                }
            }
        }
    }

}
