<?php

namespace App\Controller\User;

use App\Entity\User\ApiToken;
use App\Entity\User\User;
use App\Entity\User\UserEmail;
use App\Entity\User\UserNetwork;
use App\Repository\UserNetworkRepository;
use App\Service\Files\UserFilesManager;
use App\Utils\Env;
use App\ValueObject\User\UserId;
use Google\Client as GoogleClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Oauth2GoogleController extends AbstractController
{
    private function getGoogleClient(): GoogleClient
    {
        $client = new GoogleClient();
        $client->setApplicationName("SwipeRead");
        $client->setClientId(Env::get('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(Env::get('GOOGLE_CLIENT_SECRET'));

        return $client;
    }

    private function getGoogleRedirectUrl(): string
    {
        return Env::get('APP_URL') . '/api/v1/oauth2/google';
    }

    #[Route('/api/v1/oauth2/google/login')]
    public function loginWithGoogle(): Response
    {
        $client = $this->getGoogleClient();
        $client->setRedirectUri($this->getGoogleRedirectUrl());
        $client->addScope(['profile', 'email']);
        $googleUrl = $client->createAuthUrl();

        // todo store previous url in cookies
        // then redirect back to the same page where user was

        return $this->redirect($googleUrl);
    }

    #[Route('/api/v1/oauth2/google', name: 'oauth2_google')]
    public function loginWithGoogleProcess(
        Request $request,
        UserNetworkRepository $networks,
        UserFilesManager $files
    ): Response
    {
        $client = $this->getGoogleClient();
        $client->setRedirectUri($this->getGoogleRedirectUrl());
        $token = $client->fetchAccessTokenWithAuthCode($request->get('code'));
        $client->setAccessToken($token);
        $token_data = $client->verifyIdToken();

        $networkId = $token_data['sub'];
        $userNetwork = $networks->findOne(UserNetwork::NETWORK_GOOGLE, $networkId);
        $em = $this->getDoctrine()->getManager();

        if ($userNetwork === null) {
            $userEmail = new UserEmail($token_data['email'], $token_data['email_verified']);
            $user = new User(
                UserId::generate(),
                $token_data['given_name'],
                $token_data['family_name'],
                $userEmail,
                UserNetwork::NETWORK_GOOGLE,
                $networkId
            );

            $em->persist($user);
        } else {
            $user = $userNetwork->getUser();
        }

        $files->saveUserAvatar($user->getId(), $token_data['picture']);

        $apiToken = new ApiToken($user);
        $em->persist($apiToken);
        $em->flush();

        $response = $this->redirect(Env::get('APP_URL'));
        $response->headers->setCookie(Cookie::create('API_TOKEN', $apiToken->getToken()));
        $response->headers->setCookie(Cookie::create('IS_LOGGED_IN', '1', httpOnly: false));
        return $response;
    }
}
