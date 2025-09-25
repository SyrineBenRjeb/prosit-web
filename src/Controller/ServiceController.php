<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServiceController extends AbstractController
{
    // Question 1 : récupérer le paramètre sans afficher le nom
   #[Route('/service/param/{name}', name: 'service_param')]
public function showParam(string $name): Response
{
    return new Response('<h1>Nom reçu : ' . htmlspecialchars($name) . '</h1>');
}


    // Question 2 : afficher "service" suivi du nom
    #[Route('/service/show/{name}', name: 'service_show')]
    public function showService(string $name): Response
    {
        return new Response('<h1>service ' . htmlspecialchars($name) . '</h1>');
    }

//  C'est ici que tu appelles le fichier Twig
       #[Route('/service/render/{name}', name: 'service_render')]
    public function renderService(string $name): Response
    {
        return $this->render('service/showService.html.twig', [
            'name' => $name,
        ]);
    }
}
    

    

        


