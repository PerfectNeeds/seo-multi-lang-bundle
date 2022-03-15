<?php

namespace PN\SeoBundle\Listener;

use Doctrine\ORM\EntityManagerInterface;
use PN\SeoBundle\Entity\Redirect404;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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
            $url = $this->getUrl($event->getRequest());
            if (strpos($url, "/admin/") === false) {
                $redirectPage = $this->em->getRepository(Redirect404::class)->findOneBy(["from" => $url]);
                if ($redirectPage) {
                    $event->setResponse(new RedirectResponse($redirectPage->getTo()));
                }
            }
        }
    }

    private function getUrl(Request $request):string
    {
        $url = $request->getUri();

        if (strpos($url, "?") !== false) {
            $queryParameters = substr($request->getRequestUri(), strpos($request->getRequestUri(), "?"));
            return substr($url, 0, strpos($url, "?")).$queryParameters;
        }

        return $url;
    }
}
