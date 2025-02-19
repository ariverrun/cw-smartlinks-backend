<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Repository\InputUrlRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InputUrlRepositoryInterface::class)]
#[ORM\UniqueConstraint(columns: ['url_pattern'])]
final class InputUrl
{
    public const DEFAULT_PRIORITY = 0;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: RouteStep::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private RouteStep $initialRouteStep;

    /**
     * @var Collection<int, RouteStep> $routeSteps
     */
    #[ORM\OneToMany(targetEntity: RouteStep::class, mappedBy: 'inputUrl', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $routeSteps;

    public function __construct(
        #[ORM\Column(length: 2048)]
        private string $urlPattern,
        #[ORM\Column]
        private int $priority = self::DEFAULT_PRIORITY,
        #[ORM\Column]
        private bool $isActive = true,
    ) {
        $this->routeSteps = new ArrayCollection();
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

    public function getInitialRouteStep(): RouteStep
    {
        return $this->initialRouteStep;
    }

    public function setInitialRouteStep(RouteStep $initialRouteStep): self
    {
        $this->initialRouteStep = $initialRouteStep;

        return $this;
    }

    /**
     * @return Collection<int, RouteStep>
     */
    public function getRouteSteps(): Collection
    {
        return $this->routeSteps;
    }

    public function addRouteStep(RouteStep $routeStep): self
    {
        if (!$this->routeSteps->contains($routeStep)) {
            $this->routeSteps->add($routeStep);
            $routeStep->setInputUrl($this);
        }

        return $this;
    }

    public function removeRouteStep(RouteStep $routeStep): self
    {
        if ($this->routeSteps->removeElement($routeStep)) {
            if ($routeStep->getInputUrl() === $this) {
                $routeStep->setInputUrl(null);
            }
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