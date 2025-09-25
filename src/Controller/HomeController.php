<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return new Response('<h1>Bonjour mes étudiants</h1>');
    }

    // Méthode ajoutée : redirige vers la route 'home' (donc vers index())
    #[Route('/go-to-index', name: 'go_to_index')]
    public function goToIndex(): RedirectResponse
    {
        return $this->redirectToRoute('home');
    }
}
