<?php

declare(strict_types=1);

namespace App\Infrastructure\ValueResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Attribute\AsTargetedValueResolver;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;

#[AsTargetedValueResolver('request_for_redirect')]
final class RequestForRedirectValueResolver implements ValueResolverInterface
{
    public function __construct(
        private readonly DenormalizerInterface $denormalizer,
        private readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * @return iterable<mixed>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $requestData = $this->collectRequestData($request);

        $argumentDto = $this->denormalizer->denormalize($requestData, $argument->getType());

        $violationsList = $this->validator->validate($argumentDto);

        if ($violationsList->count() > 0) {
            throw new InvalidArgumentException();
        }

        return [$argumentDto];
    }

    /**
     * @return array{headers: array<string, list<string|null>>, locale: string, requestPath: string, requestTime: string}
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
            'ip' => $request->getClientIp(),
        ];
    }
}
