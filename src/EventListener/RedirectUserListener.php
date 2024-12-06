<?php

namespace App\EventListener;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Response;

class RedirectUserListener extends AbstractController
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelRequest(): Response
    {
        if ($this->isUserLogged() === null) {
            dump('1');
            return $this->redirectToRoute('app_login');
        }
    }

    private function isUserLogged()
    {
        return $this->tokenStorage->getToken()?->getUser();
    }
}