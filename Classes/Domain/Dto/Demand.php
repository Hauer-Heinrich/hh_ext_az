<?php
declare(strict_types=1);

namespace HauerHeinrich\HhextAz\Domain\Dto;

use TYPO3\CMS\Core\Utility\GeneralUtility;

final readonly class Demand {
    /**
     * @param int[] $recordUids
     * @param int[] $categoryUids
     */
    private function __construct(
        public array $recordUids,
        public array $categoryUids,
        public string $categoryConjunction,
        public string $sortField,
        public string $sortOrder,
    ) {
    }

    /** @param array<string, mixed> $row tt_content-Row des List-Plugins */
    public static function fromContentRow(array $row): self {
        return new self(
            GeneralUtility::intExplode(',', (string)($row['tx_hhextaz_records'] ?? ''), true),
            GeneralUtility::intExplode(',', (string)($row['tx_hhextaz_categories'] ?? ''), true),
            (string)($row['tx_hhextaz_category_conjunction'] ?? 'or'),
            (string)($row['tx_hhextaz_sort_field'] ?? 'sorting'),
            (string)($row['tx_hhextaz_sort_order'] ?? 'asc'),
        );
    }
}
