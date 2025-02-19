<?php

declare(strict_types=1);

namespace App\UI\Http\Controller\Redirection;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;

final class RedirectController extends AbstractController
{
    #[Route('/{any?}', name: 'app_redirect')]
    public function __invoke(): RedirectResponse
    {
        return new RedirectResponse('https://google.com');
    }
}