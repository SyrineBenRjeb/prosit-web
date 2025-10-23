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
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->redirectToRoute('book_list');
    }

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

    #[Route('/book/edit/{id}', name: 'book_edit')]
    public function editBook(?Book $book, Request $request, EntityManagerInterface $em): Response
    {
        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé');
        }

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

    #[Route('/book/delete/{id}', name: 'book_delete')]
    public function deleteBook(?Book $book, EntityManagerInterface $em): Response
    {
        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé');
        }

        $author = $book->getAuthor();
        if ($author) {
            $author->setNbBooks($author->getNbBooks() - 1);
            $em->persist($author);
        }

        $em->remove($book);
        $em->flush();

        $this->addFlash('success', 'Livre supprimé avec succès !');
        return $this->redirectToRoute('book_list');
    }

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

    #[Route('/book/{id}', name: 'book_show', requirements: ['id' => '\d+'])]
    public function show(int $id, BookRepository $bookRepo): Response
    {
        $book = $bookRepo->find($id);

        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé');
        }

        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }

    #[Route('/book/search', name: 'book_search')]
    public function search(Request $request, BookRepository $bookRepo): Response
    {
        $ref = $request->query->get('ref');
        $books = [];

        if ($ref) {
            $books = $bookRepo->searchBookByRef($ref);
        }

        return $this->render('book/search_results.html.twig', [
            'books' => $books,
            'searchRef' => $ref,
        ]);
    }

    #[Route('/book/update-category', name: 'book_update_category')]
    public function updateCategory(BookRepository $bookRepo, EntityManagerInterface $em): Response
    {
        $affectedRows = $bookRepo->updateCategoryFromTo('Science-Fiction', 'Romance');

        $this->addFlash('success', sprintf(
            'Catégorie des livres Science-Fiction modifiée en Romance avec succès ! (%d livres modifiés)',
            $affectedRows
        ));

        return $this->redirectToRoute('book_list');
    }

    #[Route('/books/romance/count', name: 'count_romance_books')]
    public function countRomanceBooks(BookRepository $bookRepository): Response
    {
        $count = $bookRepository->countBooksByCategory('Romance');

        return new Response("Nombre de livres de catégorie 'Romance' : " . $count);
    }

    #[Route('/books/published/between', name: 'books_published_between')]
    public function booksPublishedBetween(BookRepository $bookRepository): Response
    {
        $start = new \DateTime('2014-01-01');
        $end   = new \DateTime('2018-12-31');

        $books = $bookRepository->findBooksPublishedBetween($start, $end);

        $booksPublished = [];
        $booksUnpublished = [];

        foreach ($books as $book) {
            if ($book->isPublished()) {
                $booksPublished[] = $book;
            } else {
                $booksUnpublished[] = $book;
            }
        }

        return $this->render('book/list.html.twig', [
            'booksPublished' => $booksPublished,
            'booksUnpublished' => $booksUnpublished,
            'publishedCount' => count($booksPublished),
            'unpublishedCount' => count($booksUnpublished),
            'start' => $start,
            'end' => $end,
        ]);
    }
}
