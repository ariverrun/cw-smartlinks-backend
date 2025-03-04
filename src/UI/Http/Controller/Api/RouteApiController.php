<?php

declare(strict_types=1);

namespace App\UI\Http\Controller\Api;

use App\Application\Dto\RouteDto;
use App\Application\Service\Mapper\AutoMapperInterface;
use App\Application\UseCase\CreateRouteUseCaseInterface;
use App\Application\UseCase\UpdateRouteUseCaseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

final class RouteApiController extends AbstractController
{
    public function __construct(
        private readonly AutoMapperInterface $autoMapper,
    ) {
    }

    #[Route('/api/v1/route', methods: ['POST'], name: 'app_api_route_create')]
    public function create(
        #[MapRequestPayload]
        RouteRequestDto $requestDto,
        CreateRouteUseCaseInterface $useCase,
    ): JsonResponse {
        return $this->json([
            'id' => ($useCase)(
                $this->autoMapper->map($requestDto, RouteDto::class)
            ),
        ]);
    }

    #[Route('/api/v1/route/{routeId}', methods: ['PUT'], name: 'app_api_route_update')]
    public function update(
        int $routeId,
        #[MapRequestPayload]
        RouteRequestDto $requestDto,
        UpdateRouteUseCaseInterface $useCase,
    ): Response {
        ($useCase)($routeId, $this->autoMapper->map($requestDto, RouteDto::class));

        return new Response(status: Response::HTTP_OK);
    }
}
