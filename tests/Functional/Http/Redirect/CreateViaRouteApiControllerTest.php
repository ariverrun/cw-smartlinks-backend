<?php

declare(strict_types=1);

namespace Tests\Functional\Http\Redirect;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\User\UserInterface;

final class CreateViaRouteApiControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private string $validJwt;

    public function testInvalidRequestData(): void
    {
        $requestData = [
            'urlPattern' => '/test/posts',
            'priority' => 1,
            'isActive' => true,
            'initialStep' => null,
        ];
    
        $this->client->jsonRequest('POST', '/api/v1/route', $requestData, [
            'HTTP_Authorization' => 'Bearer ' . $this->validJwt,
        ]);

        $this->assertResponseStatusCodeSame(422);
    }

    public function testInvalidSchemeType(): void
    {
        $requestData = [
            'urlPattern' => '/test/hello',
            'priority' => 1,
            'isActive' => true,
            'initialStep' => [
                'type' => 'condition',
                'schemeType' => 'unknown_scheme_type',
                'schemeProps' => [
                    'foo' => 'bar',
                ],
                'onPassStep' => [
                    'type' => 'redirect',
                    'schemeType' => 'redirect',
                    'schemeProps' => [
                        'url' => 'redirect1.com/page/{foo_param}',
                    ],
                ],
                'onDeclineStep' => [
                    'type' => 'redirect',
                    'schemeType' => 'redirect',
                    'schemeProps' => [
                        'url' => 'redirect1.com/page/{foo_param}',
                    ],
                ],
            ],
        ];
    
        $this->client->jsonRequest('POST', '/api/v1/route', $requestData, [
            'HTTP_Authorization' => 'Bearer ' . $this->validJwt,
        ]);

        $this->assertResponseStatusCodeSame(422);
    }

    public function testInvalidSchemeProps(): void
    {
        $requestData = [
            'urlPattern' => '/test/{foo_param}/posts',
            'priority' => 1,
            'isActive' => true,
            'initialStep' => [
                'type' => 'condition',
                'schemeType' => 'datetime_range',
                'schemeProps' => [
                    'from' => date('Y-m-d H:i:s', time() - 86400),
                    'to' => date('Y-m-d H:i:s', time() + 86400),
                ],
                'onPassStep' => [
                    'type' => 'redirect',
                    'schemeType' => 'redirect',
                    'schemeProps' => [
                        'invalid_field' => 'redirect1.com/page/{foo_param}',
                    ],
                ],
                'onDeclineStep' => [
                    'type' => 'redirect',
                    'schemeType' => 'redirect',
                    'schemeProps' => [
                        'url' => 'redirect1.com/page/{foo_param}',
                    ],
                ],
            ],
        ];
    
        $this->client->jsonRequest('POST', '/api/v1/route', $requestData, [
            'HTTP_Authorization' => 'Bearer ' . $this->validJwt,
        ]);

        $this->assertResponseStatusCodeSame(422);
    }

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient();

        $userMock = $this->createMock(UserInterface::class);

        $userMock->expects($this->any())
                    ->method('getUserIdentifier')
                    ->willReturn('tgrtgr');
        
        $jwtTokenManager = $this->getContainer()->get(JWTTokenManagerInterface::class);

        $this->validJwt = $jwtTokenManager->create($userMock);        
    }    
}