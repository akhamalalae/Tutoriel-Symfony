<?php

namespace App\Controller\Navbar\Messaging;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreateMessageController extends AbstractController
{
    #[Route('/create/message', name: 'app_create_message')]
    public function create()
    {
        return $this->render('message/create.html.twig');
    }
}
