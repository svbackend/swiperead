<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class BaseApiController extends AbstractController
{
    public function err(string $error, int $code = 400): JsonResponse
    {
        return $this->json(['error' => $error], $code);
    }

    public function ack(int $code = 200): JsonResponse
    {
        return $this->json(null, $code);
    }
}
