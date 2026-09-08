<?php
declare(strict_types=1);

namespace HauerHeinrich\HhExtAz\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use HauerHeinrich\HhExtAz\Domain\Model\DefaultEntry;
use HauerHeinrich\HhExtAz\Domain\Repository\EntryRepository;
use HauerHeinrich\HhExtAz\Service\LetterGroupService;
use HauerHeinrich\HhExtAz\Domain\Dto\Demand;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class EntryController extends ActionController {

    public function __construct(
        protected readonly EntryRepository $entryRepository,
        protected readonly LetterGroupService $letterGroupService,
    ) {
    }

    /**
     * Alphabetisch gruppierte Liste aller Eintraege.
     */
    public function listAction(): ResponseInterface {
        $contentObjectData = $this->request->getAttribute('currentContentObject')?->data ?? [];
        $demand = Demand::fromContentRow($contentObjectData);

        $recordUids = $demand->recordUids;
        $categoryUids = $demand->categoryUids;
        $categoryConjunction = $demand->categoryConjunction;
        $sortField = $demand->sortField;
        $sortOrder = $demand->sortOrder;

        if ($recordUids !== []) {
            $az = $this->entryRepository->findByUids(
                $recordUids,
                $categoryUids,
                $categoryConjunction,
                $sortField,
                $sortOrder
            );
        } else {
            $storagePids = $this->resolveStoragePids($contentObjectData);
            $az = $storagePids === []
                ? []
                : $this->entryRepository->findByDemand(
                    $storagePids,
                    $categoryUids,
                    $categoryConjunction,
                    $sortField,
                    $sortOrder
                )->toArray();
        }

        $groups = $this->letterGroupService->buildGroups(
            $az,
            (string)($this->settings['menuSorting'] ?? '')
        );

        $this->view->assignMultiple([
            'data' => $contentObjectData,
            'groups' => $groups,
            'words' => [],
        ]);

        return $this->htmlResponse();
    }

    /**
     * Detailansicht - nur fuer record_type "default" verfuegbar.
     */
    public function showAction(DefaultEntry $entry): ResponseInterface {
        $contentObjectData = $this->request->getAttribute('currentContentObject')?->data ?? [];

        $this->view->assignMultiple([
            'data' => $contentObjectData,
            'entry' => $entry,
        ]);

        return $this->htmlResponse();
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
