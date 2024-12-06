<?php

namespace App\Controller\Navbar\Notification;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
    public function notificationAction()
    {
        return $this->render('nav-bar/notifications.html.twig');
    }
}
