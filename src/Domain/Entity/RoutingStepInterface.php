<?php

declare(strict_types=1);

namespace App\Domain\Entity;

/**
 * @phpstan-type SchemeProps array<string,mixed>
 */
interface RoutingStepInterface
{
    public function getOnPassStep(): ?RoutingStepInterface;

    public function setOnPassStep(?RoutingStepInterface $onPassStep): RoutingStepInterface;

    public function getOnDeclineStep(): ?RoutingStepInterface;

    public function setOnDeclineStep(?RoutingStepInterface $onDeclineStep): RoutingStepInterface;

    public function getSchemeType(): string;

    public function setSchemeType(string $schemeType): RoutingStepInterface;

    /**
     * @return SchemeProps
     */
    public function getSchemeProps(): array;

    /**
     * @param SchemeProps $schemeProps
     */
    public function setSchemeProps(array $schemeProps): RoutingStepInterface;

    public function getRoute(): ?RouteInterface;

    public function setRoute(?RouteInterface $route): RoutingStepInterface;
}
