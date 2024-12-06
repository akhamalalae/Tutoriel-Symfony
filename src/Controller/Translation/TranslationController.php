<?php

namespace App\Controller\Translation;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TranslationController extends AbstractController
{
    public function translationAction(Request $request)
    {
        $uri = $request->attributes->get('_route');
        dump($uri);
        return $this->render('nav-bar/translations.html.twig', [
            //'currentPath' => $uri,
        ]);
    }
}
