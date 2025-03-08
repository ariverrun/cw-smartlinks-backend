<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Enum\RoutingStepType;
use Doctrine\ORM\Mapping as ORM;

/**
 * @phpstan-type SchemeProps array<string,mixed>
 */
#[ORM\Entity]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string')]
#[ORM\DiscriminatorMap([
    RoutingStepType::CONDITION->value => Condition::class,
    RoutingStepType::REDIRECT->value => Redirect::class,
])]
class RoutingStep implements RoutingStepInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Route::class, inversedBy: 'steps')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    protected ?RouteInterface $route;

    #[ORM\OneToOne(targetEntity: RoutingStep::class, cascade: ['remove'], orphanRemoval: true)]
    protected ?RoutingStepInterface $onPassStep = null;

    #[ORM\OneToOne(targetEntity: RoutingStep::class, cascade: ['remove'], orphanRemoval: true)]
    protected ?RoutingStepInterface $onDeclineStep = null;

    #[ORM\Column]
    protected string $schemeType;

    /**
     * @var SchemeProps $schemeProps
     */
    #[ORM\Column]
    protected array $schemeProps;

    final public function __construct()
    {
    }

    final public function setRoute(?RouteInterface $route): static
    {
        $this->route = $route;

        return $this;
    }

    final public function getOnPassStep(): ?RoutingStepInterface
    {
        return $this->onPassStep;
    }

    final public function setOnPassStep(?RoutingStepInterface $onPassStep): static
    {
        $this->onPassStep = $onPassStep;

        return $this;
    }

    final public function getOnDeclineStep(): ?RoutingStepInterface
    {
        return $this->onDeclineStep;
    }

    final public function setOnDeclineStep(?RoutingStepInterface $onDeclineStep): static
    {
        $this->onDeclineStep = $onDeclineStep;

        return $this;
    }

    final public function getSchemeType(): string
    {
        return $this->schemeType;
    }

    final public function setSchemeType(string $schemeType): static
    {
        $this->schemeType = $schemeType;

        return $this;
    }

    /**
     * @return SchemeProps
     */
    final public function getSchemeProps(): array
    {
        return $this->schemeProps;
    }

    /**
     * @param SchemeProps $schemeProps
     */
    final public function setSchemeProps(array $schemeProps): static
    {
        $this->schemeProps = $schemeProps;

        return $this;
    }
}
