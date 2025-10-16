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
    public function showAuthor($name)
    {
        return $this->render(
            'author/show.html.twig'
            ,
            ['nom' => $name, 'prenom' => 'ben foulen']
        );
    }

    #[Route('/ShowAll', name: 'ShowAll')]
    public function ShowAll(AuthorRepository $repo)
    {
        $authors = $repo->findAll();
        return $this->render(
            'author/showAll.html.twig'
            ,
            ['list' => $authors]
        );
    }

    #[Route('/addStat', name: 'addStat')]
    public function addStat(ManagerRegistry $doctrine)
    {
        $author = new Author();
        $author->setEmail('Test@gmail.com');
        $author->setUsername('foulen');
        $author->setNbBooks(0);
        $em = $doctrine->getManager();
        $em->persist($author);
        $em->flush();

        // return new Response("Author added succesfully");
        return $this->redirectToRoute('ShowAll');
    }

    #[Route('/addForm', name: 'addForm')]
    public function addForm(ManagerRegistry $doctrine, Request $request)
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->add('add', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em = $doctrine->getManager();
            $em->persist($author);
            $em->flush();

            // return new Response("Author added succesfully");
            return $this->redirectToRoute('ShowAll');
        }
        return $this->render(
            'author/add.html.twig',
            ['formulaire' => $form->createView()]
        );
    }

    #[Route('/deleteAuthor/{id}', name: 'deleteAuthor')]
    public function deleteAuthor($id, AuthorRepository $repo, ManagerRegistry $manager)
    {
        $author = $repo->find($id);
        $em = $manager->getManager();
        $em->remove($author);
        $em->flush();
        return $this->redirectToRoute('ShowAll');
    }

    #[Route('/showAuthorDetails/{id}', name: 'showAuthorDetails')]
    public function showAuthorDetails($id, AuthorRepository $repo)
    {
        $author = $repo->find($id);
        return $this->render('author/showDetails.html.twig', ['author' => $author]);
    }
    #[Route("/update/{id}" , name :"update")]
    public function update(ManagerRegistry $doctrine , $id , AuthorRepository $repository , Request $request):Response{
        $author = $repository->find($id);
        $em = $doctrine->getManager();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            return new Response("Author updated successfully");
        }

        return $this->render("author/update.html.twig",[
            'form' => $form->createView(),
        ]);
    }


}