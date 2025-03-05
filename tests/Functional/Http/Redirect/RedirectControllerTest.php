<?php

declare(strict_types=1);

namespace Tests\Functional\Http\Redirect;

use App\Application\UseCase\GetRedirectUrlForHttpRequestUseCaseInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Exception;

final class RedirectControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    public function testRouteIsNotFound(): void
    {
        $this->client->request('GET', '/unknown_route');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testServerError(): void
    {
        $getRedirectUrlForHttpRequestUseCaseMock = $this->createMock(GetRedirectUrlForHttpRequestUseCaseInterface::class);
        $getRedirectUrlForHttpRequestUseCaseMock->expects($this->once())
                                                ->method('__invoke')
                                                ->willReturnCallback(function(): never {
                                                    throw new Exception();
                                                });

        $container = $this->client->getContainer();

        $container->set(GetRedirectUrlForHttpRequestUseCaseInterface::class, $getRedirectUrlForHttpRequestUseCaseMock);

        $this->client->request('GET', '/any_route');

        $this->assertResponseStatusCodeSame(500);
    }  

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient();
    }    
}