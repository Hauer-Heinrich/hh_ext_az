<?php
declare(strict_types=1);

namespace HauerHeinrich\HhExtAz\Domain\Model;

/**
 * record_type "file": Eintrag, der auf eine Datei URL verlinkt.
 */
class FileEntry extends Entry {
    protected string $link = '';
    protected int $forceDownload = 0;

    public function getLink(): string { return $this->link; }
    public function setLink(string $value): void { $this->link = $value; }

    public function getForceDownload(): int { return $this->forceDownload; }
    public function setForceDownload(int $value): void { $this->forceDownload = $value; }

    public function getRecordType(): string { return self::TYPE_FILE; }
}
