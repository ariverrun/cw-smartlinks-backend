<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Service\Routing;

use App\Infrastructure\Routing\RedirectionContext;
use PHPUnit\Framework\TestCase;

final class RedirectionContextTest extends TestCase
{
    public function testContextParamsManaging(): void
    {
        $params = [
            'foo' => 'bar',
        ];

        $context = new RedirectionContext($params);

        $this->assertTrue($context->hasParameter('foo'));
        $this->assertEquals('bar', $context->getParameter('foo'));
        $this->assertFalse($context->hasParameter('not_foo'));
        $this->assertNull($context->getParameter('not_foo'));
    }
}