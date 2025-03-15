<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @implements UserProviderInterface<ApiUser>
 */
final class ApiUserProvider implements UserProviderInterface
{
    public function refreshUser(UserInterface $user): UserInterface
    {
        if ($user instanceof ApiUser) {
            return $user;
        }

        throw new UnsupportedUserException();
    }

    public function supportsClass(string $class): bool
    {
        return ApiUser::class === $class;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return new ApiUser($identifier);
    }
}
