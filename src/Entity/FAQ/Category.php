<?php

declare(strict_types=1);

namespace LML\SDK\Entity\FAQ;

use LogicException;
use LML\SDK\Attribute\Entity;
use LML\SDK\Entity\ModelInterface;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Repository\FAQ\CategoryRepository;

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
class Category implements ModelInterface
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

    public function getId(): string
    {
        return $this->id ?? throw new LogicException('Entity has not been persisted yet.');
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
