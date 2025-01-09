<?php

namespace App\Controller\Navbar\Profile;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    public function profileAction()
    {
        return $this->render('nav-bar/profil.html.twig');
    }
}
