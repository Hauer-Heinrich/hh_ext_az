<?php
declare(strict_types=1);

namespace HauerHeinrich\HhExtAz\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;
use HauerHeinrich\HhExtAz\Domain\Model\Entry;

/**
 * @extends Repository<Entry>
 */
class EntryRepository extends Repository {
    protected $defaultOrderings = [
        'title' => QueryInterface::ORDER_ASCENDING,
    ];

    /**
     * Find A-Z records from given storage folders, optionally filtered by categories.
     *
     * @param int[] $storagePids
     * @param int[] $categoryUids
     * @return QueryResultInterface<Entry>
     */
    public function findByDemand(
        array $storagePids,
        array $categoryUids = [],
        string $categoryConjunction = 'or',
        string $sortField = 'sorting',
        string $sortOrder = 'asc'
    ): QueryResultInterface {
        $query = $this->createQuery();
        $query->getQuerySettings()->setStoragePageIds($storagePids);
        $this->applyCategoryConstraint($query, $categoryUids, $categoryConjunction);
        $this->applyOrdering($query, $sortField, $sortOrder);

        return $query->execute();
    }

    /**
     * Volltextsuche in title, teaser und description.
     * Alle Suchwoerter muessen vorkommen (AND), jeweils in einem der Felder (OR).
     *
     * @param list<string> $words
     * @return QueryResultInterface<Entry>
     */
    public function findBySearchWords(array $words): QueryResultInterface {
        $query = $this->createQuery();
        $constraints = [];

        foreach ($words as $word) {
            $word = trim($word);
            if ($word === '') {
                continue;
            }
            $like = '%' . addcslashes($word, '_%') . '%';
            $constraints[] = $query->logicalOr(
                $query->like('title', $like),
                $query->like('teaser', $like),
                $query->like('description', $like),
            );
        }

        if ($constraints !== []) {
            $query->matching($query->logicalAnd(...$constraints));
        }

        return $query->execute();
    }

    /**
     * @param int[] $categoryUids
     */
    protected function applyCategoryConstraint(
        QueryInterface $query,
        array $categoryUids,
        string $categoryConjunction
    ): void {
        $constraint = $this->buildCategoryConstraint($query, $categoryUids, $categoryConjunction);

        if ($constraint !== null) {
            $query->matching($constraint);
        }
    }

    /**
     * @param int[] $categoryUids
     */
    protected function buildCategoryConstraint(
        QueryInterface $query,
        array $categoryUids,
        string $categoryConjunction
    ): object|null {
        $categoryUids = array_values(array_filter(array_map('intval', $categoryUids)));

        if ($categoryUids === []) {
            return null;
        }

        $constraints = [];
        foreach ($categoryUids as $categoryUid) {
            $constraints[] = $query->contains('categories', $categoryUid);
        }

        if (count($constraints) === 1) {
            return $constraints[0];
        }

        return $categoryConjunction === 'and'
            ? $query->logicalAnd(...$constraints)
            : $query->logicalOr(...$constraints);
    }

    protected function applyOrdering(QueryInterface $query, string $sortField, string $sortOrder): void {
        $allowedFields = ['sorting', 'question', 'crdate'];

        if (!in_array($sortField, $allowedFields, true)) {
            $sortField = 'sorting';
        }

        $direction = $sortOrder === 'desc'
            ? QueryInterface::ORDER_DESCENDING
            : QueryInterface::ORDER_ASCENDING;

        $query->setOrderings([$sortField => $direction]);
    }
}
