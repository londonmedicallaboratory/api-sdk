<?php

declare(strict_types=1);

namespace LML\SDK\Entity\FAQ;

use Stringable;
use LML\SDK\Attribute\Entity;
use LML\SDK\Entity\ModelInterface;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Repository\FAQ\CategoryRepository;
use LML\SDK\Exception\EntityNotPersistedException;

/**
 * @psalm-type S = array{
 *     id?: ?string,
 *     type: string,
 *     name: string,
 * }
 *
 * @implements ModelInterface<S>
 */
#[Entity(repositoryClass: CategoryRepository::class, baseUrl: 'faq/category')]
class Category implements ModelInterface, Stringable
{
    /**
     * @param LazyValueInterface<CategoryTypeEnum> $type
     * @param LazyValueInterface<string> $name
     * @param LazyValueInterface<list<FAQ>> $faqs
     * @param string|null $id
     */
    public function __construct(
        protected LazyValueInterface $type,
        protected LazyValueInterface $name,
        protected LazyValueInterface $faqs,
        protected ?string $id = null,
    )
    {
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getId(): string
    {
        return $this->id ?? throw new EntityNotPersistedException();
    }

    public function getType(): CategoryTypeEnum
    {
        return $this->type->getValue();
    }

    public function getName(): string
    {
        return $this->name->getValue();
    }

    /**
     * @return list<FAQ>
     */
    public function getFaqs(): array
    {
        return $this->faqs->getValue();
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'type' => $this->getType()->value,
            'name' => $this->getName(),
        ];
    }
}
