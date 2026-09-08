<?php
declare(strict_types=1);

namespace HauerHeinrich\HhExtAz\Domain\Model;

use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\Domain\Model\Category;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Basisklasse aller Eintraege (Single Table Inheritance).
 */
class Entry extends AbstractEntity {
    public const TYPE_DEFAULT = 'default';
    public const TYPE_INTERNAL = 'internal';
    public const TYPE_EXTERNAL = 'external';

    protected string $title = '';
    protected ?string $teaser = '';

    /**
     * @var ObjectStorage<Category>
     */
    #[Extbase\ORM\Lazy]
    protected ObjectStorage $categories;

    public function __construct() {
        $this->initializeObject();
    }

    /**
     * Called again with persistence, as __construct() is not invoked then.
     */
    public function initializeObject(): void {
        $this->categories ??= new ObjectStorage();
    }

    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): void { $this->title = $title; }

    public function getTeaser(): string { return (string)$this->teaser; }
    public function setTeaser(?string $teaser): void { $this->teaser = $teaser; }

    public function getRecordType(): string { return self::TYPE_DEFAULT; }

    /**
     * @return ObjectStorage<Category>
     */
    public function getCategories(): ObjectStorage { return $this->categories; }
    /**
     * @param ObjectStorage<Category> $categories
     */
    public function setCategories(ObjectStorage $categories): void { $this->categories = $categories; }
}
