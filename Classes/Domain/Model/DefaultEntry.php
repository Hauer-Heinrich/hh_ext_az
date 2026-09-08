<?php
declare(strict_types=1);

namespace HauerHeinrich\HhExtAz\Domain\Model;

use TYPO3\CMS\Extbase\Domain\Model\FileReference;

/**
 * record_type "default": Eintrag mit eigener Detailseite (sprechende URL via Slug).
 */
class DefaultEntry extends Entry {
    protected string $slug = '';
    protected ?string $description = '';
    protected ?FileReference $image = null;

    public function getSlug(): string { return $this->slug; }
    public function setSlug(string $slug): void { $this->slug = $slug; }

    public function getDescription(): string { return (string)$this->description; }
    public function setDescription(?string $description): void { $this->description = $description; }

    public function getImage(): ?FileReference { return $this->image; }
    public function setImage(?FileReference $image): void { $this->image = $image; }

    public function getRecordType(): string { return self::TYPE_DEFAULT; }
}
