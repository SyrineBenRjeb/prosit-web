<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Author;
use App\Form\BookType;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    // Page d'accueil du book controller
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->redirectToRoute('book_list');
    }

    // Ajouter un livre
    #[Route('/book/add', name: 'addBook')]
    public function addBook(Request $request, EntityManagerInterface $em): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $book->setPublished(true);

            $author = $book->getAuthor();
            if ($author) {
                $author->setNbBooks($author->getNbBooks() + 1);
                $em->persist($author);
            }

            $em->persist($book);
            $em->flush();

            $this->addFlash('success', 'Livre ajouté avec succès !');
            return $this->redirectToRoute('book_list');
        }

        return $this->render('book/add.html.twig', [
            'formBook' => $form->createView(),
        ]);
    }

    // Liste des livres
    #[Route('/books', name: 'book_list')]
    public function list(BookRepository $bookRepo): Response
    {
        $booksPublished = $bookRepo->findBy(['published' => true]);
        $booksUnpublished = $bookRepo->findBy(['published' => false]);

        return $this->render('book/list.html.twig', [
            'booksPublished' => $booksPublished,
            'booksUnpublished' => $booksUnpublished,
            'publishedCount' => count($booksPublished),
            'unpublishedCount' => count($booksUnpublished),
        ]);
    }

    // Modifier un livre
    #[Route('/book/edit/{id}', name: 'book_edit')]
    public function editBook(Book $book, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Livre modifié avec succès !');
            return $this->redirectToRoute('book_list');
        }

        return $this->render('book/edit.html.twig', [
            'formBook' => $form->createView(),
            'book' => $book,
        ]);
    }

    // Supprimer un livre
    #[Route('/book/delete/{id}', name: 'book_delete')]
    public function deleteBook(Book $book, EntityManagerInterface $em): Response
    {
        $author = $book->getAuthor();
        if ($author) {
            $author->setNbBooks($author->getNbBooks() - 1);
        }

        $em->remove($book);
        $em->flush();

        $this->addFlash('success', 'Livre supprimé avec succès !');
        return $this->redirectToRoute('book_list');
    }

    // Supprimer les auteurs sans livre
    #[Route('/authors/delete-empty', name: 'author_delete_empty')]
    public function deleteEmptyAuthors(AuthorRepository $authorRepo, EntityManagerInterface $em): Response
    {
        $authors = $authorRepo->findBy(['nbBooks' => 0]);
        foreach ($authors as $author) {
            $em->remove($author);
        }
        $em->flush();

        $this->addFlash('success', 'Auteurs sans livre supprimés avec succès !');
        return $this->redirectToRoute('book_list');
    }
}