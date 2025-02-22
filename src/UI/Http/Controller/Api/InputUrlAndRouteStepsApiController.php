<?php

declare(strict_types=1);

namespace App\UI\Http\Controller\Api;

use App\Application\Dto\InputUrlAndRouteStepsDto;
use App\Application\Service\Mapper\AutoMapperInterface;
use App\Application\UseCase\CreateInputUrlAndRouteStepsUseCaseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

final class InputUrlAndRouteStepsApiController extends AbstractController
{
    public function __construct(
        private readonly AutoMapperInterface $autoMapper,
    ) {
    }

    #[Route('/api/input_and_route_steps', methods: ['POST'], name: 'app_api_input_and_route_steps_create')]
    public function create(
        #[MapRequestPayload]
        InputUrlAndRouteStepsCreateRequestDto $requestDto,
        CreateInputUrlAndRouteStepsUseCaseInterface $useCase,
    ): JsonResponse {
        return $this->json([
            'id' => ($useCase)(
                $this->autoMapper->map($requestDto, InputUrlAndRouteStepsDto::class)
            ),
        ]);
    }
}
