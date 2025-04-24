<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    // Route pour la page d'accueil
    #[Route("/", name: "home")]
    function index(Request $request): Response
    {
        // Redirection vers la vue
        return $this->render('home/index.html.twig');
    }
}