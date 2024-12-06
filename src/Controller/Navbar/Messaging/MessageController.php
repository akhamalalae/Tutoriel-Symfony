<?php

namespace App\Controller\Navbar\Messaging;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    public function messagesAction()
    {
        return $this->render('nav-bar/messages.html.twig');
    }
}
