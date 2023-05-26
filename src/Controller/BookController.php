<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BookController extends AbstractController {
  #[Route('/api/books', name: 'books', methods: ['GET'])]
  public function getAllBooks(BookRepository $bookRepository, SerializerInterface $serializerInterface): JsonResponse {
    $bookList = $bookRepository->findAll();
    $jsonBookList = $serializerInterface->serialize($bookList, 'json', ['groups' => 'getBooks']);
    return new JsonResponse($jsonBookList, Response::HTTP_OK, [], true);
  }

  #[Route('/api/book/{id}', name: 'book', methods: ['GET'])]
  public function getOneBook(Book $book, SerializerInterface $serializerInterface): JsonResponse {
    $jsonBook = $serializerInterface->serialize($book, 'json', ['groups' => 'getBooks']);
    return new JsonResponse($jsonBook, Response::HTTP_OK, [], true);
  }

  #[Route('/api/delete/book/{id}', name: 'delete-book', methods: ['DELETE'])]
  public function deleteOneBook(Book $book, EntityManagerInterface $entityManagerInterface): JsonResponse {
    $entityManagerInterface->remove($book);
    $entityManagerInterface->flush();
    return new JsonResponse(null, Response::HTTP_NO_CONTENT);
  }

  #[Route('/api/create/book', name: 'create-book', methods: ['POST'])]
  #[IsGranted('ROLE_ADMIN', message: "Vous n'avez pas les droits pour créer un livre")]
  public function createOneBook(Request $request, SerializerInterface $serializerInterface, EntityManagerInterface $entityManagerInterface, UrlGeneratorInterface $urlGeneratorInterface, AuthorRepository $authorRepository, ValidatorInterface $validatorInterface): JsonResponse {
    $book = $serializerInterface->deserialize($request->getContent(), Book::class, 'json');

    $content = $request->toArray();
    $idAuthor = $content['idAuthor'] ?? -1;

    $book->setAuthor($authorRepository->find($idAuthor));

    $errors = $validatorInterface->validate($book);
    if ($errors->count() > 0) {
      return new JsonResponse($serializerInterface->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
      // throw new HttpException(JsonResponse::HTTP_BAD_REQUEST, "La requête est invalide");
    }

    $entityManagerInterface->persist($book);
    $entityManagerInterface->flush();

    $jsonBook = $serializerInterface->serialize($book, 'json', ['groups' => 'getBooks']);
    $location = $urlGeneratorInterface->generate('book', ['id' => $book->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

    return new JsonResponse($jsonBook, Response::HTTP_CREATED, ["Location" => $location], true);
  }

  #[Route('/api/update/book/{id}', name: 'update-book', methods: ['PUT'])]
  public function updateOneBook(Book $curentBook, Request $request, SerializerInterface $serializerInterface, EntityManagerInterface $entityManagerInterface, AuthorRepository $authorRepository): JsonResponse {
    $updateBook = $serializerInterface->deserialize($request->getContent(), Book::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $curentBook]);

    $content = $request->toArray();
    $idAuthor = $content['idAuthor'] ?? -1;

    $updateBook->setAuthor($authorRepository->find($idAuthor));

    $entityManagerInterface->persist($updateBook);
    $entityManagerInterface->flush();

    return new JsonResponse(null, Response::HTTP_NO_CONTENT);
  }
}
