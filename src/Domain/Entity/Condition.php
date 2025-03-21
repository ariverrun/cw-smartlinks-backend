<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Exception\InvalidEntityException;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Condition extends RoutingStep
{
    /**
     * @throws InvalidEntityException
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function validate(): void
    {
        if (
            null === $this->onPassStep
            || null === $this->onDeclineStep
        ) {
            throw new InvalidEntityException('Condition entity has to have both onPassStep and onDeclineStep');
        }
    }
}
