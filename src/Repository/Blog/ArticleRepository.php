<?php

declare(strict_types=1);

namespace LML\SDK\Repository\Blog;

use LML\SDK\Entity\Blog\Article;
use LML\SDK\Entity\Blog\ArticleInterface;
use LML\SDK\Service\API\AbstractRepository;

/**
 * @psalm-import-type S from ArticleInterface
 *
 * @extends AbstractRepository<S, Article, array{
 *     category?: string,
 * }>
 */
class ArticleRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer): Article
    {
        $id = $entity['id'];

        return new Article(
            id     : $id,
            title  : $entity['title'],
            slug   : $entity['slug'],
            content: $entity['content'],
        );
    }
}
