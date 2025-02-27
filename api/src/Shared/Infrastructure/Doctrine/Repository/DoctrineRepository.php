<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Doctrine\Repository;

use App\Shared\Domain\Repository\PaginatorInterface;
use App\Shared\Domain\Repository\RepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @template T of object
 *
 * @implements RepositoryInterface<T>
 */
abstract class DoctrineRepository implements RepositoryInterface
{
    private ?int $page = null;

    private ?int $itemsPerPage = null;

    private QueryBuilder $queryBuilder;

    public function __construct(
        protected EntityManagerInterface $entityManager,
        string $entityClass,
        string $alias,
    ) {
        $this->queryBuilder = $this->entityManager->createQueryBuilder()
            ->select($alias)
            ->from($entityClass, $alias);
    }

    public function getIterator(): \Iterator
    {
        if (($paginator = $this->paginator()) instanceof \App\Shared\Domain\Repository\PaginatorInterface) {
            yield from $paginator;

            return;
        }

        yield from $this->queryBuilder->getQuery()->getResult();
    }

    public function count(): int
    {
        $paginator = $this->paginator() ?? new Paginator(clone $this->queryBuilder);

        return count($paginator);
    }

    public function paginator(): ?PaginatorInterface
    {
        if (null === $this->page || null === $this->itemsPerPage) {
            return null;
        }

        $firstResult = ($this->page - 1) * $this->itemsPerPage;
        $maxResults = $this->itemsPerPage;

        $repository = $this->filter(static function (QueryBuilder $queryBuilder) use ($firstResult, $maxResults): void {
            $queryBuilder->setFirstResult($firstResult)->setMaxResults($maxResults);
        });

        /** @var Paginator<T> $paginator */
        $paginator = new Paginator($repository->queryBuilder->getQuery());

        return new DoctrinePaginator($paginator);
    }

    public function withoutPagination(): static
    {
        $cloned = clone $this;
        $cloned->page = null;
        $cloned->itemsPerPage = null;

        return $cloned;
    }

    public function withPagination(int $page, int $itemsPerPage): static
    {
        if ($page < 0) {
            $page = 0;
        }

        if ($itemsPerPage < 1) {
            $itemsPerPage = 1;
        }

        $cloned = clone $this;
        $cloned->page = $page;
        $cloned->itemsPerPage = $itemsPerPage;

        return $cloned;
    }

    /**
     * @return static<T>
     */
    protected function filter(callable $filter): static
    {
        $cloned = clone $this;
        $filter($cloned->queryBuilder);

        return $cloned;
    }

    protected function query(): QueryBuilder
    {
        return clone $this->queryBuilder;
    }

    protected function __clone()
    {
        $this->queryBuilder = clone $this->queryBuilder;
    }
}
