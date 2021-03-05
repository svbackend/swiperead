<?php

namespace App\Controller;

use App\Entity\User\User;
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

    public function getUser(): User
    {
        $user = parent::getUser();

        if ($user instanceof User === false) {
            throw new \ErrorException('Unexpected User object returned');
        }

        return $user;
    }
}
