<?php

namespace App\Controller;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

final class BookController extends AbstractController
{
    // Requête en GET

    #[Route('/book-test', name: 'app_book')]
    public function index(): JsonResponse
    {
        $maVariable = [
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/BookController.php',
        ];

        // return $this->json($maVariable);

        return new JsonResponse($maVariable, Response::HTTP_OK, [], false);
    }

    #[Route('/booksAll', name: 'app_book_listfull')]
    public function showBooksListFull(SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {
        $booksList = $entityManager->getRepository(Book::class)->findAll();

        if ($booksList) {
            $booksListJSON = $serializer->serialize($booksList, 'json');
            return new JsonResponse($booksListJSON, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    #[Route('/books', name: 'app_book', methods: ['GET'])]
    public function showBooksList(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {

        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);

        $booksList = $entityManager->getRepository(Book::class)->findAllWithPage($page, $limit);

        if ($booksList) {
            $booksListJSON = $serializer->serialize($booksList, 'json', ['groups' => 'getBooks']);
            return new JsonResponse($booksListJSON, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    #[Route('/books/{id}', name: 'app_book_details', methods: ['GET'])]
    public function showBookDetails(int $id, SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {
        $book = $entityManager->getRepository(Book::class)->find($id);

        if ($book) {
            $bookJSON = $serializer->serialize($book, 'json');
            return new JsonResponse($bookJSON, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    // Requete en POST

    #[Route('/books', name: 'app_add_book', methods: ['POST'])]
    public function addNewBook(SerializerInterface $serializer, EntityManagerInterface $entityManager, Request $request): JsonResponse
    {

        $newBook = $serializer->deserialize($request->getContent(), Book::class, 'json');

        $entityManager->persist($newBook);
        $entityManager->flush();

        $newBookResume = $serializer->serialize($newBook, 'json');
        return new JsonResponse($newBookResume, Response::HTTP_CREATED, [], true);
    }


    // Requete en PUT

    #[Route('/books/{id}', name: 'app_edit_book', methods: ['PUT'])]
    public function editBook(int $id, Book $currentBook, SerializerInterface $serializer, EntityManagerInterface $entityManager, Request $request): JsonResponse
    {

        $editBook = $serializer->deserialize(
            $request->getContent(),
            Book::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentBook]
        );

        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    // Requete en DELETE

    #[Route('/books/{id}', name: 'app_delete_book', methods: ['DELETE'])]
    public function deleteBook(Book $currentBook, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($currentBook);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
