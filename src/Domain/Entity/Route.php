<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Repository\RouteRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RouteRepositoryInterface::class)]
#[ORM\UniqueConstraint(columns: ['url_pattern'])]
final class Route implements RouteInterface
{
    public const DEFAULT_PRIORITY = 0;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    /**
     * @phpstan-ignore property.unusedType
     */
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: RoutingStep::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private RoutingStepInterface $initialStep;

    /**
     * @var Collection<int, RoutingStepInterface> $steps
     */
    #[ORM\OneToMany(targetEntity: RoutingStep::class, mappedBy: 'route', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $steps;

    public function __construct(
        #[ORM\Column(length: 2048)]
        private string $urlPattern,
        #[ORM\Column]
        private int $priority = self::DEFAULT_PRIORITY,
        #[ORM\Column]
        private bool $isActive = true,
    ) {
        $this->steps = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrlPattern(): string
    {
        return $this->urlPattern;
    }

    public function setUrlPattern(string $urlPattern): self
    {
        $this->urlPattern = $urlPattern;

        return $this;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getInitialStep(): RoutingStepInterface
    {
        return $this->initialStep;
    }

    public function setInitialStep(RoutingStepInterface $initialStep): self
    {
        $this->initialStep = $initialStep;

        return $this;
    }

    /**
     * @return Collection<int, RoutingStepInterface>
     */
    public function getSteps(): Collection
    {
        return $this->steps;
    }

    public function addStep(RoutingStepInterface $step): self
    {
        if (!$this->steps->contains($step)) {
            $this->steps->add($step);
            $step->setRoute($this);
        }

        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }
}
