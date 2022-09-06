<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Repository;

use LML\SDK\Tests\AbstractTest;
use LML\SDK\Entity\Blog\Article;
use LML\SDK\Entity\Blog\Category;
use LML\SDK\Repository\Blog\ArticleRepository;
use LML\SDK\Repository\Blog\CategoryRepository;

class BlogRepositoryTest extends AbstractTest
{
    public function testGetCategories(): void
    {
        self::bootKernel();
        $repo = $this->getCategoryRepository();
        $category = $repo->paginate(await: true)->first();
        self::assertInstanceOf(Category::class, $category);
    }

    public function testGetArticles(): void
    {
        self::bootKernel();
        $repo = $this->getArticleRepository();
        $article = $repo->paginate(await: true)->first();
        self::assertInstanceOf(Article::class, $article);
    }

    private function getCategoryRepository(): CategoryRepository
    {
        return $this->getService(CategoryRepository::class);
    }

    private function getArticleRepository(): ArticleRepository
    {
        return $this->getService(ArticleRepository::class);
    }
}
