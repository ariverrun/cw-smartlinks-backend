<?php

declare(strict_types=1);

namespace App\UI\Http\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DevController extends AbstractController
{
    public function __construct(
    ) {
    }

    #[Route('/dev{any?}', name: 'app_dev')]
    public function __invoke(): Response
    {
        return $this->json([
            'foo' => 'bar',
        ]);
    }
}