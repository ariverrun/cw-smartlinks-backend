<?php

declare(strict_types=1);

namespace App\UI\Http\Controller\Redirection;

use App\Application\Dto\HttpRequestDto;
use App\Application\Exception\MachingRouteIsNotFoundException;
use App\Application\Service\Mapper\AutoMapperInterface;
use App\Application\UseCase\GetRedirectUrlForHttpRequestUseCaseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

final class RedirectController extends AbstractController
{
    public function __construct(
        private readonly AutoMapperInterface $autoMapper,
    ) {
    }

    #[Route('/{path}', name: 'app_redirect', requirements: ['path' => '.+'])]
    public function __invoke(
        #[ValueResolver('request_for_redirect')]
        RequestForRedirectDto $requestDto,
        GetRedirectUrlForHttpRequestUseCaseInterface $useCase,
    ): RedirectResponse {
        try {
            return $this->redirect(
                ($useCase)(
                    $this->autoMapper->map($requestDto, HttpRequestDto::class)
                )
            );
        } catch (Throwable $e) {
            throw match (true) {
                $e instanceof MachingRouteIsNotFoundException => $this->createNotFoundException(),
                default => $e,
            };
        }
    }
}
