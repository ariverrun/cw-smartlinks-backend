<?php

declare(strict_types=1);

namespace App\Infrastructure\Routing;

use App\Application\Service\Routing\RoutingMapProviderInterface;
use App\Application\Service\Routing\RoutingMapConstantsHolder;
use App\Domain\Repository\InputUrlRepositoryInterface;

class RoutingMapProvider implements RoutingMapProviderInterface
{
    public function __construct(
        private readonly InputUrlRepositoryInterface $inputUrlRepository,
    ) {
    }

    public function getRoutingMap(): array
    {
        $routingMap = [];

        $inputUrls = $this->inputUrlRepository->findAllActiveDescByPriority();

        foreach ($inputUrls as $inputUrl) {
            $urlPatternParts = explode('/', $inputUrl->getUrlPattern());
            unset($urlPatternParts[0]);

            if (!isset($routingMap[$inputUrl->getPriority()])) {
                $routingMap[$inputUrl->getPriority()] = [];
            }

            $tail = &$routingMap[$inputUrl->getPriority()];
            $urlPatternPartsCount = count($urlPatternParts);

            foreach ($urlPatternParts as $i => $part) {
                if (str_starts_with($part, '{') && str_ends_with($part, '}')) {
                    $part = RoutingMapConstantsHolder::REQUIRED_PARAM;
                } elseif ('*' === $part) {
                    $part = RoutingMapConstantsHolder::MATCHES_ALL;
                }

                if (!isset($tail[$part])) {
                    $tail[$part] = [];
                }

                if ($i === $urlPatternPartsCount) {
                    $tail[$part][RoutingMapConstantsHolder::TERMINAL_KEY] = $inputUrl->getId();
                } else {
                    $tail = &$tail[$part];
                }
            }
        }

        return $routingMap;
    }
}
