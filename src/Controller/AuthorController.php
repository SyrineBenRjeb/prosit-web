<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }
    #[Route( '/show/{name}/{prenom}',name: 'showAuthor')]
public function showAuthor(string $name, string $prenom):Response{
       return $this->render('author/show.html.twig' , [
        'nom' => $name, 
        'prenom' => $prenom
    ]);

    }
}
