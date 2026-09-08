<?php
declare(strict_types=1);

namespace HauerHeinrich\HhExtAz\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use HauerHeinrich\HhExtAz\Domain\Repository\EntryRepository;
use HauerHeinrich\HhExtAz\Service\LetterGroupService;

class SearchController extends ActionController {

    public function __construct(
        protected readonly EntryRepository $entryRepository,
        protected readonly LetterGroupService $letterGroupService,
    ) {
    }

    /**
     * Volltextsuche (Server-Fallback).
     *
     * Liegt das Such-Plugin auf derselben Seite wie das List-Plugin,
     * uebernimmt JavaScript das Live-Filtern und Highlighten direkt in
     * der Liste. Ohne JavaScript (oder auf einer eigenen Suchseite)
     * rendert diese Action die Treffer serverseitig inkl. Highlighting.
     */
    public function searchAction(string $q = ''): ResponseInterface {
        $contentObjectData = $this->request->getAttribute('currentContentObject')?->data ?? [];

        $q = trim($q);
        $words = $q === '' ? [] : (preg_split('/\s+/u', $q, -1, PREG_SPLIT_NO_EMPTY) ?: []);

        $groups = [];
        if ($words !== []) {
            $entries = $this->entryRepository->findBySearchWords($words);
            $groups = $this->letterGroupService->buildGroups(
                $entries,
                (string)($this->settings['menuSorting'] ?? '')
            );
        }

        $this->view->assignMultiple([
            'data' => $contentObjectData,
            'q' => $q,
            'words' => $words,
            'groups' => $groups,
            'hasQuery' => $q !== '',
        ]);

        return $this->htmlResponse();
    }
}
