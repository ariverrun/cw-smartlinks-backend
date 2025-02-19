<?php

declare(strict_types=1);

namespace App\Infrastructure\ValueResolver;

use App\UI\Http\Controller\Redirection\RequestForRedirectDto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;

final class RequestForRedirectValueResovler implements ValueResolverInterface
{
    public function __construct(
        private readonly DenormalizerInterface $denormalizer,
        private readonly ValidatorInterface $validator,
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $argumentType = $argument->getType();

        if (RequestForRedirectDto::class !== $argumentType) {
            return [];
        }

        $requestData = $this->collectRequestData($request);

        $argumentDto = $this->denormalizer->denormalize($requestData, RequestForRedirectDto::class);

        $violationsList = $this->validator->validate($argumentDto);

        if ($violationsList->count() > 0) {
            throw new InvalidArgumentException(\sprintf('Request can not be resolved to %s', RequestForRedirectDto::class));
        }

        return [$argumentDto];
    }

    /**
     * @return array{headers: array, locale: string, requestPath: string, requestTime: string}
     */
    private function collectRequestData(Request $request): array
    {
        return [
            'requestPath' => $request->getPathInfo(),
            'locale' => $request->getLocale(),
            'headers' => $request->headers->all(),
            'requestTime' => DateTimeImmutable::createFromFormat(
                'U.u',
                (string)$request->server->get('REQUEST_TIME_FLOAT')
            )
            ->format(
                DateTimeInterface::ATOM
            ),
        ];
    }
}
