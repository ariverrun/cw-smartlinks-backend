<?php

declare(strict_types=1);

namespace Tests\Functional\Http\Redirect;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class UpdateViaRouteApiControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    public function testInvalidRequestData(): void
    {
        $requestData = [
            'urlPattern' => '/test/posts',
            'priority' => 1,
            'isActive' => true,
            'initialStep' => null,
        ];
    
        $this->client->jsonRequest('PUT', '/api/v1/route/1', $requestData);

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
    
        $this->client->jsonRequest('PUT', '/api/v1/route/1', $requestData);

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
    
        $this->client->jsonRequest('PUT', '/api/v1/route/1', $requestData);

        $this->assertResponseStatusCodeSame(422);
    }

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient();
    }    
}