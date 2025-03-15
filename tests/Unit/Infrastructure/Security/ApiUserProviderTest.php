<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Security;

use App\Infrastructure\Security\ApiUser;
use App\Infrastructure\Security\ApiUserProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;

final class ApiUserProviderTest extends TestCase
{
    public function testUserClassSupport(): void
    {
        $apiUserProvider = new ApiUserProvider();

        $apiUserMock = $this->createMock(ApiUser::class);

        $this->assertTrue($apiUserProvider->supportsClass(ApiUser::class));
        $this->assertEqualsCanonicalizing($apiUserMock, $apiUserProvider->refreshUser($apiUserMock));
        
        $otherUserMock = $this->createMock(UserInterface::class);

        $this->assertFalse($apiUserProvider->supportsClass($otherUserMock::class));
        $this->expectException(UnsupportedUserException::class);
        $apiUserProvider->refreshUser($otherUserMock);
    }
}