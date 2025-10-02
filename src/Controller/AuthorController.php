<?php

namespace App\Controller;

use App\Repository\AuthorRepository;
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
    #[Route( '/show/{name}_{prenom}',name: 'showAuthor')]
public function showAuthor(string $name, string $prenom):Response{
       return $this->render('author/show.html.twig' , [
        'nom' => $name, 
        'prenom' => $prenom
    ]);


    }
    #[Route('/ShowAll' ,name:'ShowAll')]
   public function ShowAll(AuthorRepository $repo):Response{
    $authors=$repo->findAll();
    return $this->render('author/ShowAll.html.twig', ['authors' => $authors]);
   }


}
