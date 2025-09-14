<?php

declare(strict_types=1);

namespace App\UI\Api;

use App\Application\DTO\CreateUserRequest;
use App\Application\UseCase\CreateUserUseCase;
use App\Application\UseCase\GetUserUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/users', name: 'api_users_')]
final class UserController extends AbstractController
{
    public function __construct(
        private readonly CreateUserUseCase $createUserUseCase,
        private readonly GetUserUseCase $getUserUseCase,
        private readonly ValidatorInterface $validator
    ) {
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!$data) {
                return new JsonResponse(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            $createUserRequest = new CreateUserRequest(
                $data['email'] ?? '',
                $data['firstName'] ?? '',
                $data['lastName'] ?? ''
            );

            // Validation basique
            $errors = $this->validator->validate($createUserRequest);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }
                return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
            }

            $userResponse = $this->createUserUseCase->execute($createUserRequest);

            return new JsonResponse([
                'data' => [
                    'id' => $userResponse->id,
                    'email' => $userResponse->email,
                    'firstName' => $userResponse->firstName,
                    'lastName' => $userResponse->lastName,
                    'fullName' => $userResponse->fullName,
                    'createdAt' => $userResponse->createdAt,
                    'updatedAt' => $userResponse->updatedAt,
                ]
            ], Response::HTTP_CREATED);

        } catch (\DomainException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_CONFLICT);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function get(string $id): JsonResponse
    {
        try {
            $userResponse = $this->getUserUseCase->execute($id);

            return new JsonResponse([
                'data' => [
                    'id' => $userResponse->id,
                    'email' => $userResponse->email,
                    'firstName' => $userResponse->firstName,
                    'lastName' => $userResponse->lastName,
                    'fullName' => $userResponse->fullName,
                    'createdAt' => $userResponse->createdAt,
                    'updatedAt' => $userResponse->updatedAt,
                ]
            ]);

        } catch (\DomainException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
