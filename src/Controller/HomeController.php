<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\MessageGenerator;

class HomeController extends AbstractController
{
    // Page d'accueil principale
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return new Response('<h1>Bonjour mes étudiants</h1>');
    }

    // Page pour afficher la citation du jour
    #[Route('/quote', name: 'quote')]
    public function quote(MessageGenerator $messageGenerator): Response
    {
        $message = $messageGenerator->getHappyMessage();
        return new Response("<h1>Citation du jour :</h1><p>$message</p>");
    }

    // Redirection vers la page d'accueil
    #[Route('/go-to-index', name: 'go_to_index')]
    public function goToIndex(): RedirectResponse
    {
        return $this->redirectToRoute('home');
    }
}
