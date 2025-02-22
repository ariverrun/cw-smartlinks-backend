<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @phpstan-type SchemeProps array<string,mixed>
 */
#[ORM\Entity]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string')]
#[ORM\DiscriminatorMap([
    'condition' => Condition::class,
    'redirect' => Redirect::class,
])]
class RoutingStep
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Route::class, inversedBy: 'steps')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    protected ?Route $route;

    #[ORM\OneToOne(targetEntity: RoutingStep::class)]
    protected ?RoutingStep $onPassStep = null;

    #[ORM\OneToOne(targetEntity: RoutingStep::class)]
    protected ?RoutingStep $onDeclineStep = null;

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

    final public function getId(): ?int
    {
        return $this->id;
    }

    final public function getRoute(): ?Route
    {
        return $this->route;
    }

    final public function setRoute(?Route $route): static
    {
        $this->route = $route;

        return $this;
    }

    final public function getOnPassStep(): ?RoutingStep
    {
        return $this->onPassStep;
    }

    final public function setOnPassStep(?RoutingStep $onPassStep): static
    {
        $this->onPassStep = $onPassStep;

        return $this;
    }

    final public function getOnDeclineStep(): ?RoutingStep
    {
        return $this->onDeclineStep;
    }

    final public function setOnDeclineStep(?RoutingStep $onDeclineStep): static
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
