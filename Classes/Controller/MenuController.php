<?php
declare(strict_types=1);

namespace HauerHeinrich\HhExtAz\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use HauerHeinrich\HhExtAz\Domain\Repository\EntryRepository;
use HauerHeinrich\HhExtAz\Service\LetterGroupService;
use HauerHeinrich\HhExtAz\Service\ListPluginResolver;
use HauerHeinrich\HhExtAz\Domain\Dto\Demand;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class MenuController extends ActionController {

    public function __construct(
        protected readonly EntryRepository $entryRepository,
        protected readonly LetterGroupService $letterGroupService,
        protected readonly ListPluginResolver $listPluginResolver,
    ) {
    }

    /**
     * Sprungmenue: zeigt alle Buchstaben aus settings.menuSorting und
     * verlinkt aktive Buchstaben auf den jeweiligen Gruppen-Anker der Liste.
     */
    public function menuAction(): ResponseInterface {
        $contentObjectData = $this->request->getAttribute('currentContentObject')?->data ?? [];
        $listPid = (int)$this->request->getAttribute('frontend.page.information')?->getId();
        $listRow = $this->listPluginResolver->resolveOnPage($listPid);

        $entries = [];
        if ($listRow !== null) {
            $demand = Demand::fromContentRow($listRow);
            $entries = $demand->recordUids !== []
                ? $this->entryRepository->findByUids($demand->recordUids, $demand->categoryUids, $demand->categoryConjunction, 'title', 'asc')
                : $this->findByStorageOf($listRow, $demand); // resolveStoragePids($listRow) + findByDemand
        }

        $tokens = $this->letterGroupService->buildMenu(
            $entries,
            (string)($this->settings['menuSorting'] ?? '')
        );

        $cacheCollector = $this->request->getAttribute('frontend.cache.collector');
        $cacheCollector?->addCacheTags(new \TYPO3\CMS\Core\Cache\CacheTag('pageId_' . $listPid));

        $this->view->assignMultiple([
            'data' => $contentObjectData,
            'tokens' => $tokens,
            'listPid' => $listPid
        ]);

        return $this->htmlResponse();
    }

    /**
     * @return \HHExt\HhextAz\Domain\Model\Entry[]
     */
    private function findByStorageOf(array $listRow, Demand $demand): array {
        $storagePids = $this->resolveStoragePids($listRow);
        if ($storagePids === []) {
            return [];
        }

        return $this->entryRepository
            ->findByDemand(
                $storagePids,
                $demand->categoryUids,
                $demand->categoryConjunction,
                'title',
                'asc'
            )
            ->toArray();
    }

    /**
     * Resolve storage pids from the content element's "pages" and
     * "recursive" fields. Falls back to the current page.
     *
     * @param array<string, mixed> $contentObjectData
     * @return int[]
     */
    protected function resolveStoragePids(array $contentObjectData): array {
        $pids = GeneralUtility::intExplode(',', (string)($contentObjectData['pages'] ?? ''), true);
        if ($pids === []) {
            $currentPageId = (int)($this->request->getAttribute('frontend.page.information')?->getId() ?? 0);

            return $currentPageId > 0 ? [$currentPageId] : [];
        }

        $recursive = (int)($contentObjectData['recursive'] ?? 0);
        if ($recursive > 0) {
            $pageRepository = GeneralUtility::makeInstance(PageRepository::class);
            $pids = $pageRepository->getPageIdsRecursive($pids, $recursive);
        }

        return array_values(array_unique(array_map('intval', $pids)));
    }
}
