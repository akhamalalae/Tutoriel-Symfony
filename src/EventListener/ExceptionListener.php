<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ExceptionListener
{
    private RouterInterface $router;
    private RequestStack $requestStack;

    public function __construct(RouterInterface $router, RequestStack $requestStack)
    {
        $this->router = $router;
        $this->requestStack = $requestStack;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // Vérifier si l'exception est une erreur 404
        if ($exception instanceof NotFoundHttpException) {
            // Rediriger vers une page personnalisée
            $locale = $this->requestStack->getCurrentRequest()->getLocale();

            $params['_locale'] = $locale;

            $response = new RedirectResponse($this->router->generate('app_custom_404', $params));
            
            $event->setResponse($response);
        }
    }
}
