<?php

declare(strict_types=1);

namespace App\Infrastructure\Routing;

use App\Application\Service\Routing\RoutingMapNodeInterface;

final class RoutingMapNode implements RoutingMapNodeInterface
{
    /**
     * @var array<string,RoutingMapNodeInterface[]>
     */
    private array $nodesByKey = [];

    /**
     * @var RoutingMapNodeInterface[]
     */
    private array $requiredParamNodes = [];

    /**
     * @var RoutingMapNodeInterface[]
     */
    private array $matchingAllNodes = [];

    private ?int $terminalRouteId = null;

    public function getNodesByKey(string $routePartKey): array
    {
        return $this->nodesByKey[$routePartKey] ?? [];
    }

    public function getNodesByRequiredParam(): array
    {
        return $this->requiredParamNodes;
    }

    public function getNodesMatchingAll(): array
    {
        return $this->matchingAllNodes;
    }

    public function getTerminalRouteId(): ?int
    {
        return $this->terminalRouteId;
    }

    public function addKeyNode(string $routePartKey, RoutingMapNodeInterface $node): void
    {
        if (!isset($this->nodesByKey[$routePartKey])) {
            $this->nodesByKey[$routePartKey] = [];
        }

        $this->nodesByKey[$routePartKey][] = $node;
    }

    public function addRequiredParamNode(RoutingMapNodeInterface $node): void
    {
        $this->requiredParamNodes[] = $node;
    }

    public function addMatchingAllNode(RoutingMapNodeInterface $node): void
    {
        $this->matchingAllNodes[] = $node;
    }

    public function setTerminalRouteId(?int $routeId): void
    {
        $this->terminalRouteId = $routeId;
    }
}
