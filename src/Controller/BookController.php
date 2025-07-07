<?php

namespace App\Controller;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

final class BookController extends AbstractController
{
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

    #[Route('/booksListFull', name: 'app_book_listfull')]
    public function showBooksListFull(SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {
        $booksList = $entityManager->getRepository(Book::class)->findAll();

        if ($booksList) {
            $booksListJSON = $serializer->serialize($booksList, 'json');
            return new JsonResponse($booksListJSON, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }



    #[Route('/books', name: 'app_book')]
    public function showBooksList(SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {
        $booksList = $entityManager->getRepository(Book::class)->findAll();

        if ($booksList) {
            $booksListJSON = $serializer->serialize($booksList, 'json', ['groups' => 'getBooks']);
            return new JsonResponse($booksListJSON, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }



    #[Route('/books/{id}', name: 'app_book_details')]
    public function showBookDetails(int $id, SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {
        $book = $entityManager->getRepository(Book::class)->find($id);

        if ($book) {
            $bookJSON = $serializer->serialize($book, 'json');
            return new JsonResponse($bookJSON, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}
