<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends BaseApiController
{
    public function __construct(
        private BookRepository $books
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
}
