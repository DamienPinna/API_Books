<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController {
  #[Route('/api/books', name: 'books', methods: ['GET'])]
  public function getAllBooks(BookRepository $bookRepository, SerializerInterface $serializerInterface): JsonResponse {
    $bookList = $bookRepository->findAll();
    $jsonBookList = $serializerInterface->serialize($bookList, 'json');
    return new JsonResponse($jsonBookList, Response::HTTP_OK, [], true);
  }

  #[Route('/api/book/{id}', name: 'book', methods: ['GET'])]
  public function getOneBook(Book $book, SerializerInterface $serializerInterface): JsonResponse {
    $jsonBook = $serializerInterface->serialize($book, 'json');
    return new JsonResponse($jsonBook, Response::HTTP_OK, [], true);
  }
}
