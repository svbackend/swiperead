<?php

namespace App\Controller\User;

use App\Controller\BaseApiController;
use App\Repository\ApiTokenRepository;
use App\Security\ApiTokenAuthenticator;
use App\Service\Files\UserFilesManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MeController extends BaseApiController
{
    #[Route('/api/v1/user/me', methods: ['GET'])]
    public function me(UserFilesManager $files): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->err('You are not authenticated!', 401);
        }

        return $this->json([
            'id' => $user->getId()->getValue(),
            'name' => $user->getName(),
            'email' => $user->getEmail()->getEmail(),
            'avatar_url' => $files->getUserAvatar($user->getId())->getUrl(),
        ]);
    }

    #[Route('/api/v1/user/logout', methods: ['POST'])]
    public function logout(
        Request $request,
        ApiTokenAuthenticator $tokenAuthenticator,
        ApiTokenRepository $tokens
    ): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->err('You are not authenticated!', 401);
        }

        $token = $tokenAuthenticator->getCredentials($request);
        $apiToken = $tokens->findOneByToken($token);

        $this->getDoctrine()->getManager()->remove($apiToken);
        $this->getDoctrine()->getManager()->flush();

        $response = $this->ack();
        $response->headers->clearCookie('API_TOKEN');
        $response->headers->clearCookie('IS_LOGGED_IN');

        return $response;
    }
}
