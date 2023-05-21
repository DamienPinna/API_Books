<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;

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
}
