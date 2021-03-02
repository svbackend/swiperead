<?php

namespace App\Controller\User;

use App\Controller\BaseApiController;
use App\Entity\User\ApiToken;
use App\Entity\User\User;
use App\Entity\User\UserEmail;
use App\Entity\User\UserNetwork;
use App\Repository\UserNetworkRepository;
use App\Service\Files\UserFilesManager;
use App\Utils\Env;
use App\Utils\Json;
use App\ValueObject\User\UserId;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Oauth2FacebookController extends BaseApiController
{
    private function getRedirectUrl(): string
    {
        return urlencode(Env::get('APP_URL') . '/api/v1/oauth2/facebook');
    }

    private function getRequestUrl(): string
    {
        $clientId = Env::get('FACEBOOK_CLIENT_ID');
        $redirectUrl = $this->getRedirectUrl();
        $state = 'random';

        $url = 'https://www.facebook.com/v9.0/dialog/oauth';
        $url .= "?client_id={$clientId}&redirect_uri={$redirectUrl}&state={$state}&scope=email,public_profile";

        // todo store previous url in cookies
        // then redirect back to the same page where user was

        return $url;
    }

    #[Route('/api/v1/oauth2/facebook/login')]
    public function loginWithFacebook(): Response
    {
        $url = $this->getRequestUrl();

        // todo store previous url in cookies
        // then redirect back to the same page where user was

        return $this->redirect($url);
    }

    #[Route('/api/v1/oauth2/facebook', name: 'oauth2_facebook')]
    public function loginWithFacebookProcess(
        Request $request,
        UserNetworkRepository $networks,
        Client $client,
        UserFilesManager $files
    ): Response
    {
        $code = $request->get('code');
        $clientId = Env::get('FACEBOOK_CLIENT_ID');
        $secret = Env::get('FACEBOOK_CLIENT_SECRET');
        $requestUrl = $this->getRequestUrl();
        $url = "https://graph.facebook.com/v9.0/oauth/access_token";
        $url .= "?client_id={$clientId}&redirect_uri={$requestUrl}&client_secret={$secret}&code={$code}";

        try {
            $response = $client->get($url);
        } catch (ClientException $e) {
            return $this->render('error/error.html.twig', [
                'message' => $e->getResponse()->getBody()->getContents()
            ]);
        }

        $data = Json::decode($response->getBody()->getContents());

        $me = $client->get('https://graph.facebook.com/me?fields=email,first_name,last_name,picture&access_token=' . $data['access_token']);
        $userData = Json::decode($me->getBody()->getContents());

        $networkId = $userData['id'];
        $userNetwork = $networks->findOne(UserNetwork::NETWORK_FACEBOOK, $networkId);
        $em = $this->getDoctrine()->getManager();

        if ($userNetwork === null) {
            if (isset($userData['email'])) {
                $userEmail = new UserEmail($userData['email'], true);
            } else {
                $userEmail = new UserEmail($userData['email'], true);
            }

            $user = new User(
                UserId::generate(),
                $userData['first_name'] ?? 'John',
                $userData['last_name'] ?? 'Doe',
                $userEmail,
                UserNetwork::NETWORK_FACEBOOK,
                $networkId
            );

            $em->persist($user);
        } else {
            $user = $userNetwork->getUser();
        }

        $files->saveUserAvatar($user->getId(), $userData['picture']['data']['url']);

        $apiToken = new ApiToken($user);
        $em->persist($apiToken);
        $em->flush();

        $response = $this->redirect(Env::get('APP_URL') . '/app/find/channel');
        $response->headers->setCookie(Cookie::create('API_TOKEN', $apiToken->getToken()));
        $response->headers->setCookie(Cookie::create('IS_LOGGED_IN', '1', httpOnly: false));

        return $response;
    }

    #[Route('/api/v1/oauth2/facebook/delete')]
    public function del(): Response
    {
        return $this->ack();
    }
}
