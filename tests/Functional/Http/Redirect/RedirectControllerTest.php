<?php

declare(strict_types=1);

namespace Tests\Functional\Http\Redirect;

use App\Application\UseCase\GetRedirectUrlForHttpRequestUseCaseInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Exception;

final class RedirectControllerTest extends WebTestCase
{
    public function testRouteIsNotFound(): void
    {
        $client = static::createClient();

        $client->request('GET', '/unknown_route');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testServerError(): void
    {
        $client = static::createClient();

        $getRedirectUrlForHttpRequestUseCaseMock = $this->createMock(GetRedirectUrlForHttpRequestUseCaseInterface::class);
        $getRedirectUrlForHttpRequestUseCaseMock->expects($this->once())
                                                ->method('__invoke')
                                                ->willReturnCallback(function(): never {
                                                    throw new Exception();
                                                });

        $container = $client->getContainer();

        $container->set(GetRedirectUrlForHttpRequestUseCaseInterface::class, $getRedirectUrlForHttpRequestUseCaseMock);

        $client->request('GET', '/any_route');

        $this->assertResponseStatusCodeSame(500);
    }    
}