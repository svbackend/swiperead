<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookChapterCardRepository;
use App\Repository\BookRepository;
use App\Utils\CastHelper;
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
        // todo add voter to check permission to read the book

        $cardId = CastHelper::toInt($request->get('card_id'));
        $limit = (int)$request->get('limit', 5);

        $cards = $this->cards->findALlByBook(new BookId($id), $cardId, $limit);

        return $this->json($cards);
    }

    #[Route('/api/v1/books/{id}/bookmark', methods: ['POST'])]
    public function createBookmark(
        string $id,
        Request $request,
    ): Response
    {
        $cardId = CastHelper::toInt($request->get('card_id'));
        // todo add voter to check permission

        if (!$cardId) {
            return $this->err('You need to provide card id');
        }

        //try {
            $this->books->upsertBookmark(new BookId($id), $cardId);
        //} catch (\Throwable $e) {
            //return $this->err('Internal Error! Please try again later.', 500);
        //}

        return $this->ack();
    }
}
