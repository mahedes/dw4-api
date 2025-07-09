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
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;

final class BookController extends AbstractController
{
    // Requête en GET

    // #[Route('/book-test', name: 'app_book', methods: ['GET'])]
    // public function index(): JsonResponse
    // {
    //     $maVariable = [
    //         'message' => 'Welcome to your new controller!',
    //         'path' => 'src/Controller/BookController.php',
    //     ];

    //     // return $this->json($maVariable);

    //     return new JsonResponse($maVariable, Response::HTTP_OK, [], false);
    // }

    #[Route('api/booksAll', name: 'app_book_listfull', methods: ['GET'])]
    #[OA\Tag(name: 'Read - Lecture des données')]
    #[OA\Response(
        response: 200,
        description: 'Retourne la liste des livres complètes',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Book::class, groups: ['full']))
        )
    )]
    public function showBooksListFull(SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {
        $booksList = $entityManager->getRepository(Book::class)->findAll();

        if ($booksList) {
            $booksListJSON = $serializer->serialize($booksList, 'json');
            return new JsonResponse($booksListJSON, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }



    #[Route('api/books', name: 'app_book', methods: ['GET'])]
    #[OA\Tag(name: 'Read - Lecture des données')]
    #[OA\Response(
        response: 200,
        description: 'Retourne la liste des livres',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Book::class, groups: ['full']))
        )
    )]
    #[OA\Parameter(
        name: 'page',
        in: 'query',
        description: 'Numéro de la page désirée',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'limit',
        in: 'query',
        description: 'Nombre limite de livres affichés par page',
        schema: new OA\Schema(type: 'string')
    )]
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



    #[Route('api/books/{id}', name: 'app_book_details', methods: ['GET'])]
    #[OA\Tag(name: 'Read - Lecture des données')]
    #[OA\Response(
        response: 200,
        description: 'Retourne un seul livre',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Book::class, groups: ['full']))
        )
    )]
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

    #[Route('api/books', name: 'app_add_book', methods: ['POST'])]
    #[OA\Tag(name: 'Create - Ajout de données')]
    #[OA\Response(
        response: 201,
        description: 'Ajout d\'un livre',
        content: new OA\JsonContent(ref: new Model(type: Book::class, groups: ['full']))
    )]
    #[OA\RequestBody(
        description: "Données du livre à ajouter",
        content: new OA\JsonContent(ref: new Model(type: Book::class, groups: ['full']))
    )]
    public function addNewBook(ValidatorInterface $validator, SerializerInterface $serializer, EntityManagerInterface $entityManager, Request $request): JsonResponse
    {

        $newBook = $serializer->deserialize($request->getContent(), Book::class, 'json');

        $errors = $validator->validate($newBook);

        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $entityManager->persist($newBook);
        $entityManager->flush();

        $newBookResume = $serializer->serialize($newBook, 'json');
        return new JsonResponse($newBookResume, Response::HTTP_CREATED, [], true);
    }


    // Requete en PUT

    #[Route('api/books/{id}', name: 'app_edit_book', methods: ['PUT'])]
    #[OA\Tag(name: 'Update - Modification de données')]
    #[OA\Response(
        response: 200,
        description: 'Mise à jour d\'un livre',
        content: new OA\JsonContent(ref: new Model(type: Book::class, groups: ['full']))
    )]
    #[OA\RequestBody(
        description: "Données du livre à mettre à jour",
        content: new OA\JsonContent(ref: new Model(type: Book::class, groups: ['full']))
    )]
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

    #[Route('api/books/{id}', name: 'app_delete_book', methods: ['DELETE'])]
    #[OA\Tag(name: 'Delete - Suppression de données')]
    public function deleteBook(Book $currentBook, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($currentBook);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
