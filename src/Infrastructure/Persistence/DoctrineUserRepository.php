<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserId;
use App\Infrastructure\Persistence\Doctrine\UserDoctrineEntity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class DoctrineUserRepository implements UserRepositoryInterface
{
    /** @var EntityRepository<UserDoctrineEntity> */
    private EntityRepository $repository;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(UserDoctrineEntity::class);
    }

    public function save(User $user): void
    {
        /** @var UserDoctrineEntity|null $existingEntity */
        $existingEntity = $this->repository->find($user->id()->value());

        if ($existingEntity) {
            $existingEntity->updateFromDomain($user);
        } else {
            $entity = UserDoctrineEntity::fromDomain($user);
            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();
    }

    public function findById(UserId $id): ?User
    {
        /** @var UserDoctrineEntity|null $entity */
        $entity = $this->repository->find($id->value());

        return $entity ? $entity->toDomain() : null;
    }

    public function findByEmail(Email $email): ?User
    {
        /** @var UserDoctrineEntity|null $entity */
        $entity = $this->repository->findOneBy(['email' => $email->value()]);

        return $entity ? $entity->toDomain() : null;
    }

    public function existsByEmail(Email $email): bool
    {
        return $this->repository->count(['email' => $email->value()]) > 0;
    }

    public function delete(User $user): void
    {
        /** @var UserDoctrineEntity|null $entity */
        $entity = $this->repository->find($user->id()->value());

        if ($entity) {
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
        }
    }

    /**
     * @return User[]
     */
    public function findAll(): array
    {
        /** @var UserDoctrineEntity[] $entities */
        $entities = $this->repository->findAll();

        return array_map(fn(UserDoctrineEntity $entity) => $entity->toDomain(), $entities);
    }
}
