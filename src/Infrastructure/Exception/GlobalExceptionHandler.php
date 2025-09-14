<?php

declare(strict_types=1);

namespace App\Infrastructure\Exception;

use App\Domain\Exception\DomainException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Validator\Exception\ValidationFailedException;

final class GlobalExceptionHandler
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        // Ne traiter que les requêtes API
        if (!str_starts_with($request->getPathInfo(), '/api/')) {
            return;
        }

        $response = $this->handleException($exception);
        $event->setResponse($response);
    }

    private function handleException(\Throwable $exception): JsonResponse
    {
        // Log de l'exception
        $this->logger->error('API Exception occurred', [
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);

        return match (true) {
            $exception instanceof ValidationFailedException => $this->handleValidationException($exception),
            $exception instanceof DomainException => $this->handleDomainException($exception),
            default => $this->handleGenericException($exception),
        };
    }

    private function handleValidationException(ValidationFailedException $exception): JsonResponse
    {
        $violations = [];
        foreach ($exception->getViolations() as $violation) {
            $violations[] = [
                'property' => $violation->getPropertyPath(),
                'message' => $violation->getMessage(),
                'invalidValue' => $violation->getInvalidValue(),
            ];
        }

        return new JsonResponse([
            'error' => 'Validation failed',
            'violations' => $violations,
        ], Response::HTTP_BAD_REQUEST);
    }

    private function handleDomainException(DomainException $exception): JsonResponse
    {
        $statusCode = match ($exception::class) {
            \App\Domain\User\Exception\UserNotFoundException::class => Response::HTTP_NOT_FOUND,
            \App\Domain\User\Exception\UserAlreadyExistsException::class => Response::HTTP_CONFLICT,
            default => Response::HTTP_BAD_REQUEST,
        };

        return new JsonResponse([
            'error' => $exception->getMessage(),
        ], $statusCode);
    }

    private function handleGenericException(\Throwable $exception): JsonResponse
    {
        return new JsonResponse([
            'error' => 'Internal server error',
            'message' => 'An unexpected error occurred',
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
