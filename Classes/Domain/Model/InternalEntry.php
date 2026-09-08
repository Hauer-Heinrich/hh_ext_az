<?php
declare(strict_types=1);

namespace HauerHeinrich\HhExtAz\Domain\Model;

class InternalEntry extends Entry {
    protected string $internalLink = '';

    public function getInternalLink(): string { return $this->internalLink; }
    public function setInternalLink(string $internalLink): void { $this->internalLink = $internalLink; }

    public function getRecordType(): string { return self::TYPE_INTERNAL; }
}
