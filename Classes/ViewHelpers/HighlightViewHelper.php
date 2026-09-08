<?php
declare(strict_types=1);

namespace HauerHeinrich\HhExtAz\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Umschliesst Suchtreffer in einem Text mit <mark>.
 *
 * Verwendung:
 *   <az:highlight text="{entry.title}" words="{words}" />
 *
 * Der Text wird intern HTML-escaped, nur die <mark>-Tags bleiben aktiv.
 */
final class HighlightViewHelper extends AbstractViewHelper {
    protected $escapeOutput = false;
    protected $escapeChildren = false;

    public function initializeArguments(): void {
        $this->registerArgument('text', 'string', 'Der zu durchsuchende Text', false, '');
        $this->registerArgument('words', 'array', 'Zu markierende Suchwoerter', false, []);
    }

    public function render(): string {
        $text = (string)($this->arguments['text'] ?? '');
        if ($text === '') {
            $text = (string)$this->renderChildren();
        }
        if ($text === '') {
            return '';
        }

        $words = [];
        foreach ((array)$this->arguments['words'] as $word) {
            $word = trim((string)$word);
            if ($word !== '') {
                $words[] = $word;
            }
        }

        if ($words === []) {
            return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5);
        }

        $quoted = array_map(
            static fn(string $word): string => preg_quote($word, '/'),
            $words
        );
        $pattern = '/(' . implode('|', $quoted) . ')/iu';

        $parts = preg_split($pattern, $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        if ($parts === false) {
            return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5);
        }

        $output = '';
        foreach ($parts as $index => $part) {
            $escaped = htmlspecialchars($part, ENT_QUOTES | ENT_HTML5);
            // Ungerade Indizes sind die gefundenen Suchwoerter (Capture-Group)
            $output .= ($index % 2 === 1)
                ? '<mark class="mark">' . $escaped . '</mark>'
                : $escaped;
        }

        return $output;
    }
}
