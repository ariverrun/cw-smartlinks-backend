<?php

declare(strict_types=1);

namespace Tests\Functional;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ApplicationFullTest extends WebTestCase
{
    private KernelBrowser $client;
    
    public function testCreateRouteAndRedirect(): void
    {
        $createRouteRequestData = [
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

        $this->client->jsonRequest('POST', '/api/v1/route', $createRouteRequestData);

        $this->assertResponseStatusCodeSame(200);

        $this->client->request('GET', '/test/hello/posts');

        $this->assertResponseRedirects('redirect1.com/page/hello');

        $this->client->request('GET', '/unknown_route');

        $this->assertResponseStatusCodeSame(404);  
    }

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient();
    }

    protected function tearDown(): void
    {
        $purger = new ORMPurger(self::getContainer()->get(EntityManagerInterface::class));
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $purger->purge();
    }
}