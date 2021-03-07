<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookChapterCardRepository;
use App\Repository\BookRepository;
use App\ValueObject\Book\BookId;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends BaseApiController
{
    public function __construct(
        private BookRepository $books,
        private BookChapterCardRepository $cards,
    )
    {
    }

    #[Route('/api/v1/books')]
    public function listBooks(
        Request $request,
    ): Response
    {
        $user = $this->getUser();
        $books = $this->books->findAllByOwner($user->getId());

        return $this->json($books);
    }

    #[Route('/api/v1/books/{id}/cards')]
    public function listBookCards(
        string $id,
        Request $request,
    ): Response
    {
        $user = $this->getUser();
        $books = $this->cards->findALlByBook(new BookId($id));

        return $this->json($books);
    }
}