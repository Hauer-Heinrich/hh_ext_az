<?php

declare(strict_types=1);

namespace HauerHeinrich\HhExtAz\Domain\Model;

/**
 * record_type "external": Eintrag, der auf eine externe URL verlinkt.
 */
class ExternalEntry extends Entry {
    protected string $externalLink = '';

    public function getExternalLink(): string { return $this->externalLink; }
    public function setExternalLink(string $externalLink): void { $this->externalLink = $externalLink; }

    public function getRecordType(): string { return self::TYPE_EXTERNAL; }
}
