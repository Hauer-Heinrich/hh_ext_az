<?php
declare(strict_types=1);

namespace HauerHeinrich\HhExtAz\Domain\Model;

/**
 * record_type "text": Eintrag ohne Link (reiner Text, z. B. Hinweis/Zwischenüberschrift).
 */
class TextEntry extends Entry {
    public function getRecordType(): string { return self::TYPE_TEXT; }
}
