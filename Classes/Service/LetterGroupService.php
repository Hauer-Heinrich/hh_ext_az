<?php
declare(strict_types=1);

namespace HauerHeinrich\HhExtAz\Service;

use HauerHeinrich\HhExtAz\Domain\Model\Entry;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Gruppiert Eintraege nach Anfangsbuchstaben.
 *
 * Die Reihenfolge der Gruppen wird ueber TypoScript gesteuert
 * (plugin.tx_hhextaz.settings.menuSorting), z. B. "0-9,a,b,c,...".
 * Alles, was keinem gelisteten Buchstaben zugeordnet werden kann
 * (Ziffern, Sonderzeichen), landet in der Gruppe "0-9".
 */
final class LetterGroupService {
    public const FALLBACK_TOKEN = '0-9';

    private const TRANSLITERATION = [
        'ä' => 'a', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'å' => 'a', 'æ' => 'a',
        'ö' => 'o', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'ø' => 'o',
        'ü' => 'u', 'ù' => 'u', 'ú' => 'u', 'û' => 'u',
        'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
        'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
        'ß' => 's', 'ç' => 'c', 'ñ' => 'n', 'ý' => 'y',
    ];

    /**
     * Zerlegt den menuSorting-String in eine Token-Liste.
     * Die Fallback-Gruppe "0-9" wird garantiert (notfalls am Ende angehaengt).
     *
     * @return list<string>
     */
    public function parseTokens(string $menuSorting): array {
        $tokens = [];
        foreach (explode(',', $menuSorting) as $token) {
            $token = mb_strtolower(trim($token));
            if ($token !== '' && !in_array($token, $tokens, true)) {
                $tokens[] = $token;
            }
        }
        if ($tokens === []) {
            $tokens = range('a', 'z');
        }
        if (!in_array(self::FALLBACK_TOKEN, $tokens, true)) {
            $tokens[] = self::FALLBACK_TOKEN;
        }
        return $tokens;
    }

    /**
     * Baut die (nicht-leeren) Buchstabengruppen in konfigurierter Reihenfolge.
     *
     * @param iterable<Entry> $entries
     * @return list<array{token: string, label: string, anchor: string, entries: list<Entry>}>
     */
    public function buildGroups(iterable $entries, string $menuSorting): array {
        $tokens = $this->parseTokens($menuSorting);
        $buckets = array_fill_keys($tokens, []);

        foreach ($entries as $entry) {
            $buckets[$this->resolveToken($entry->getTitle(), $tokens)][] = $entry;
        }

        $collator = class_exists(\Collator::class) ? new \Collator('de_DE') : null;

        $groups = [];
        foreach ($tokens as $token) {
            if ($buckets[$token] === []) {
                continue;
            }
            $bucket = $buckets[$token];
            usort($bucket, static function (Entry $a, Entry $b) use ($collator): int {
                if ($collator !== null) {
                    return (int)$collator->compare($a->getTitle(), $b->getTitle());
                }
                return strcasecmp($a->getTitle(), $b->getTitle());
            });
            $groups[] = [
                'token' => $token,
                'label' => $this->buildLabel($token),
                'anchor' => $token,
                'entries' => $bucket,
            ];
        }

        return $groups;
    }

    /**
     * Baut alle Token fuer das Sprungmenue (auch leere, als inaktiv markiert).
     *
     * @param iterable<Entry> $entries
     * @return list<array{token: string, label: string, anchor: string, hasEntries: bool}>
     */
    public function buildMenu(iterable $entries, string $menuSorting): array {
        $tokens = $this->parseTokens($menuSorting);
        $counts = array_fill_keys($tokens, 0);

        foreach ($entries as $entry) {
            $counts[$this->resolveToken($entry->getTitle(), $tokens)]++;
        }

        $menu = [];
        foreach ($tokens as $token) {
            $menu[] = [
                'token' => $token,
                'label' => $this->buildLabel($token),
                'anchor' => $token,
                'hasEntries' => $counts[$token] > 0,
            ];
        }

        return $menu;
    }

    /**
     * Ermittelt die Gruppe fuer einen Titel (a-z oder Fallback "0-9").
     *
     * @param list<string> $tokens
     */
    public function resolveToken(string $title, array $tokens): string {
        $first = mb_strtolower(mb_substr(trim($title), 0, 1));
        $first = self::TRANSLITERATION[$first] ?? $first;

        if (!preg_match('/^[a-z]$/', $first)) {
            $transliterated = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $first);
            $first = mb_strtolower(substr((string)$transliterated, 0, 1));
        }

        if (preg_match('/^[a-z]$/', $first) && in_array($first, $tokens, true)) {
            return $first;
        }

        return self::FALLBACK_TOKEN;
    }

    private function buildLabel(string $token): string {
        return $token === self::FALLBACK_TOKEN ? self::FALLBACK_TOKEN : mb_strtoupper($token);
    }
}
