<?php

declare(strict_types=1);

namespace App\Domain\Entity;

interface RouteInterface
{
    public function getId(): ?int;
    public function getUrlPattern(): string;

    public function setUrlPattern(string $urlPattern): self;

    public function getPriority(): int;

    public function setPriority(int $priority): self;

    public function getInitialStep(): RoutingStepInterface;

    public function setInitialStep(RoutingStepInterface $initialStep): self;

    public function addStep(RoutingStepInterface $step): self;

    public function isActive(): bool;

    public function setIsActive(bool $isActive): self;
}
