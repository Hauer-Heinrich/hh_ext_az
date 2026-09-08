<?php
declare(strict_types=1);

namespace HauerHeinrich\HhextAz\Service;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;

final class ListPluginResolver {
    public const LIST_CTYPE = 'hhextaz_list'; // an euren tatsächlichen CType anpassen

    public function __construct(
        private readonly ConnectionPool $connectionPool,
    ) {
    }

    /**
     * Liefert die tt_content-Row des ersten List-Plugins auf der Seite.
     *
     * @return array<string, mixed>|null
     */
    public function resolveOnPage(int $pageId): ?array {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');

        $row = $queryBuilder
            ->select(
                'uid',
                'pid',
                'pages',
                'recursive',
                'tx_hhextaz_records',
                'tx_hhextaz_categories',
                'tx_hhextaz_category_conjunction',
                'tx_hhextaz_sort_field',
                'tx_hhextaz_sort_order'
            )
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq(
                    'pid',
                    $queryBuilder->createNamedParameter($pageId, Connection::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'CType',
                    $queryBuilder->createNamedParameter(self::LIST_CTYPE)
                ),
                $queryBuilder->expr()->in(
                    'sys_language_uid',
                    $queryBuilder->createNamedParameter([-1, 0], Connection::PARAM_INT_ARRAY)
                )
            )
            ->orderBy('sorting', 'ASC')
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative();

        return $row === false ? null : $row;
    }
}
