<?php

declare(strict_types=1);

namespace LML\SDK\Model\Product;

class ProductFaq implements ProductFaqInterface
{
    public function __construct(
        private string $id,
        private string $question,
        private string $answer,
        private int $priority,
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

    public function toArray()
    {
        return [
            'id'       => $this->getId(),
            'question' => $this->getQuestion(),
            'answer'   => $this->getAnswer(),
            'priority' => $this->getPriority(),
        ];
    }
}
