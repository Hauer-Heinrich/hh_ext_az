<?php
declare(strict_types=1);

namespace HauerHeinrich\HhExtAz\Domain\Model;

/**
 * record_type "internal": Eintrag, der auf eine interne URL verlinkt.
 */
class InternalEntry extends Entry {
    protected string $link = '';

    public function getLink(): string { return $this->link; }
    public function setLink(string $value): void { $this->link = $value; }

    public function getRecordType(): string { return self::TYPE_INTERNAL; }
}
