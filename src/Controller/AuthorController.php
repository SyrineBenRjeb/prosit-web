<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\HappyQuote;

final class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

    #[Route('/show/{name}', name: 'showAuthor')]
    public function showAuthor($name): Response
    {
        return $this->render('author/show.html.twig', [
            'nom' => $name,
            'prenom' => 'ben foulen'
        ]);
    }

    // ✅ Route principale : affichage de tous les auteurs + message du service
    #[Route('/ShowAll', name: 'ShowAll')]
    public function showAll(AuthorRepository $repo, HappyQuote $happyQuote): Response
    {
        $authors = $repo->findAll();
        $happyMessage = $happyQuote->getHappyMessage();

        return $this->render('author/showAll.html.twig', [
            'list' => $authors,
            'happyMessage' => $happyMessage,
        ]);
    }

    #[Route('/addStat', name: 'addStat')]
    public function addStat(ManagerRegistry $doctrine): Response
    {
        $author = new Author();
        $author->setEmail('Test@gmail.com');
        $author->setUsername('foulen');
        $author->setNbBooks(0);

        $em = $doctrine->getManager();
        $em->persist($author);
        $em->flush();

        return $this->redirectToRoute('ShowAll');
    }

    #[Route('/addForm', name: 'addForm')]
    public function addForm(ManagerRegistry $doctrine, Request $request): Response
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->add('add', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($author->getNbBooks() === null) {
                $author->setNbBooks(0);
            }

            $em = $doctrine->getManager();
            $em->persist($author);
            $em->flush();

            return $this->redirectToRoute('ShowAll');
        }

        return $this->render('author/add.html.twig', [
            'formulaire' => $form->createView()
        ]);
    }

    #[Route('/updateAuthor/{id}', name: 'updateAuthor')]
    public function updateAuthor($id, AuthorRepository $repo, ManagerRegistry $doctrine, Request $request): Response
    {
        $author = $repo->find($id);

        if (!$author) {
            throw $this->createNotFoundException("Auteur avec ID $id non trouvé !");
        }

        $form = $this->createForm(AuthorType::class, $author);
        $form->add('update', SubmitType::class, ['label' => 'Mettre à jour']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($author->getNbBooks() === null) {
                $author->setNbBooks(0);
            }

            $em = $doctrine->getManager();
            $em->flush();

            return $this->redirectToRoute('ShowAll');
        }

        return $this->render('author/update.html.twig', [
            'formulaire' => $form->createView(),
            'author' => $author
        ]);
    }

    #[Route('/deleteAuthor/{id}', name: 'deleteAuthor')]
    public function deleteAuthor($id, AuthorRepository $repo, ManagerRegistry $manager): Response
    {
        $author = $repo->find($id);

        if ($author) {
            $em = $manager->getManager();
            $em->remove($author);
            $em->flush();
        }

        return $this->redirectToRoute('ShowAll');
    }

    #[Route('/showAuthorDetails/{id}', name: 'showAuthorDetails')]
    public function showAuthorDetails($id, AuthorRepository $repo): Response
    {
        $author = $repo->find($id);

        if (!$author) {
            throw $this->createNotFoundException('Auteur non trouvé');
        }

        return $this->render('author/showDetails.html.twig', [
            'author' => $author
        ]);
    }

    #[Route('/ShowAllByEmail', name: 'ShowAllByEmail')]
    public function ShowAllByEmail(AuthorRepository $repo): Response
    {
        $authors = $repo->listAuthorByEmail();

        return $this->render('author/showAll.html.twig', [
            'list' => $authors
        ]);
    }

    #[Route("/showAllDQL", name:'showAllDQL')]
    public function showAllAuthorsDQL(AuthorRepository $repo): Response
    {
        $authors = $repo->ShowAllAuthorsDQL();

        return $this->render('author/showAll.html.twig', [
            'list' => $authors
        ]);
    }
}