<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Product;

use LML\SDK\Attribute\Entity;
use LML\SDK\Entity\ModelInterface;
use LML\SDK\Repository\ProductFaqRepository;

/**
 * @psalm-type S=array{
 *      id: string,
 *      question: string,
 *      answer: string,
 *      priority: int,
 * }
 *
 * @implements ModelInterface<S>
 */
#[Entity(repositoryClass: ProductFaqRepository::class, baseUrl: 'product_faq')]
class ProductFaq implements ModelInterface
{
    public function __construct(
        protected string $id,
        protected string $question,
        protected string $answer,
        protected int $priority,
    )
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getQuestion(): string
    {
        return $this->question;
    }

    public function getAnswer(): string
    {
        return $this->answer;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'question' => $this->getQuestion(),
            'answer' => $this->getAnswer(),
            'priority' => $this->getPriority(),
        ];
    }
}
