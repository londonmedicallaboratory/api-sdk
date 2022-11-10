<?php

declare(strict_types=1);

namespace LML\SDK\Repository\Blog;

use LML\SDK\Lazy\LazyPromise;
use LML\SDK\Entity\File\File;
use LML\SDK\Entity\Blog\Article;
use React\Promise\PromiseInterface;
use LML\SDK\Repository\FileRepository;
use LML\SDK\Service\API\AbstractRepository;

/**
 * @psalm-import-type S from Article
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
            id: $id,
            title: $entity['title'],
            slug: $entity['slug'],
            content: $entity['content'],
            logo: new LazyPromise($this->getLogo($id)),
        );
    }

    /**
     * @return PromiseInterface<?File>
     */
    private function getLogo(string $id): PromiseInterface
    {
        $url = sprintf('/blog/article/%s/logo', $id);

        return $this->get(FileRepository::class)->findOneBy(url: $url);
    }
}
