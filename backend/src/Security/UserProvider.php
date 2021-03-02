<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User\User;
use App\Repository\ApiTokenRepository;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    private UserRepository $userRepository;
    private ApiTokenRepository $apiTokenRepository;

    public function __construct(UserRepository $userRepository, ApiTokenRepository $apiTokenRepository)
    {
        $this->userRepository = $userRepository;
        $this->apiTokenRepository = $apiTokenRepository;
    }

    public function loadUserByUsername(string $email): User
    {
        $user = $this->userRepository->findOneBy([ 'email' => $email ]);

        if (!$user) {
            throw new UsernameNotFoundException("User not found by this email!");
        }

        return $user;
    }

    public function loadUserByToken(string $token): User
    {
        $apiToken = $this->apiTokenRepository->findOneBy(['token' => $token]);

        if ($apiToken === null) {
            throw new UsernameNotFoundException("User not found by this api token!");
        }

        return $apiToken->getUser();
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user): ?User
    {
        if (!$this->supportsClass(\get_class($user))) {
            throw new UnsupportedUserException(
                sprintf('Wrong User object given, expected %s, but %s given', User::class, get_class($user))
            );
        }

        /** @var $user User */
        if (null === $reloadedUser = $this->userRepository->findById($user->getId())) {
            throw new UsernameNotFoundException("User not found!");
        }

        return $reloadedUser;
    }

    public function supportsClass($class): bool
    {
        $userClass = User::class;

        return $userClass === $class || is_subclass_of($class, $userClass);
    }
}