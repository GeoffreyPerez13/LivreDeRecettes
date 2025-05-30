<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    // Route pour la page d'accueil
    #[Route("/", name: "home")]
    function index(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher, Security $security): Response
    {   
        // Redirection vers la vue
        return $this->render('home/index.html.twig');
    }
}
