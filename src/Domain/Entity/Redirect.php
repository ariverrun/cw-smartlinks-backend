<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Exception\InvalidEntityException;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Redirect extends RouteStep
{
    /**
     * @throws InvalidEntityException
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function validate(): void
    {
        if (
            null !== $this->onPassStep
            || null !== $this->onDeclineStep
        ) {
            throw new InvalidEntityException('Redirect entity can not have onPassStep or onDeclineStep');
        }
    }
}
