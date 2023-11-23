<?php

declare(strict_types=1);

namespace LML\SDK\Entity\FAQ;

use LML\SDK\Attribute\Entity;
use LML\SDK\Entity\ModelInterface;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Repository\FAQ\FAQRepository;
use LML\SDK\Exception\EntityNotPersistedException;

/**
 * @psalm-type S = array{
 *     id?: ?string,
 *     category_id: string,
 *     question: string,
 *     answer: string,
 * }
 *
 * @implements ModelInterface<S>
 */
#[Entity(repositoryClass: FAQRepository::class, baseUrl: 'faq/faq')]
class FAQ implements ModelInterface
{
    /**
     * @param LazyValueInterface<Category> $category
     * @param LazyValueInterface<string> $question
     * @param LazyValueInterface<string> $answer
     */
    public function __construct(
        protected LazyValueInterface $category,
        protected LazyValueInterface $question,
        protected LazyValueInterface $answer,
        protected ?string $id = null,
    )
    {
    }

    public function getId(): string
    {
        return $this->id ?? throw new EntityNotPersistedException();
    }

    public function getCategory(): Category
    {
        return $this->category->getValue();
    }

    public function getQuestion(): string
    {
        return $this->question->getValue();
    }

    public function getAnswer(): string
    {
        return $this->answer->getValue();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->getCategory()->getId(),
            'question' => $this->getQuestion(),
            'answer' => $this->getAnswer(),
        ];
    }
}
