<?php

namespace App\Security;

use App\Entity\User\User;
use App\Entity\User\UserRole;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AuthenticatorInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

class ApiTokenAuthenticator implements AuthenticatorInterface
{
    public function createAuthenticatedToken(UserInterface $user, string $providerKey): PostAuthenticationGuardToken
    {
        if ($user instanceof User) {
            return new PostAuthenticationGuardToken(
                $user,
                $providerKey,
                array_map(static fn (UserRole $userRole) => $userRole->getRole(), $user->getRoles()->toArray())
            );
        }

        throw new \InvalidArgumentException(
            sprintf('We expect to receive $user as %s but %s given', User::class, get_class($user))
        );
    }

    public function supports(Request $request): bool
    {
        return !empty($request->cookies->get('API_TOKEN')) || !empty($request->headers->get('api-token'));
    }

    public function getCredentials(Request $request): string
    {
        return $request->cookies->get('API_TOKEN', $request->headers->get('api-token', ''));
    }

    public function getUser($apiToken, UserProviderInterface $userProvider): User
    {
        if (!$userProvider instanceof UserProvider) {
            throw new \InvalidArgumentException(
                sprintf("Wrong UserProvider, expected %s but %s given", UserProvider::class, get_class($userProvider))
            );
        }

        return $userProvider->loadUserByToken($apiToken);
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $data = ['error' => $exception->getMessage()];

        if ($request->isXmlHttpRequest() || $request->headers->get('Content-Type') === 'application/json') {
            return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
        }

        $response = new RedirectResponse('/');
        $response->headers->clearCookie('API_TOKEN');
        $response->headers->clearCookie('IS_LOGGED_IN');
        return $response;
    }

    public function start(Request $request, AuthenticationException $authException = null): JsonResponse
    {
        $data = [
            'error' => $authException ? $authException->getMessage() : 'You must be authenticated to access this page',
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response
    {
        return null;
    }
}
