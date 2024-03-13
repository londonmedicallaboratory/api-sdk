<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\SDK\Entity\File\File;
use LML\SDK\Entity\File\Video;
use LML\SDK\Entity\Money\Price;
use LML\View\Lazy\ResolvedValue;
use LML\SDK\Lazy\ExtraLazyPromise;
use LML\SDK\Entity\Product\Product;
use React\Promise\PromiseInterface;
use LML\SDK\Entity\Shipping\Shipping;
use LML\SDK\Entity\Category\Category;
use LML\SDK\Entity\Product\ProductFaq;
use LML\SDK\Entity\Biomarker\Biomarker;
use LML\SDK\Service\API\AbstractRepository;
use LML\SDK\Exception\DataNotFoundException;
use function sprintf;

/**
 * @psalm-import-type S from Product
 *
 * @extends AbstractRepository<S, Product, array{
 *     slug?: string,
 *     search?: string,
 *     sku?: string,
 *     category_slug?: string,
 *     collection_slug?: string,
 *     product_categories?: list<string>,
 * }>
 */
class ProductRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer): Product
    {
        $priceData = $entity['price'] ?? throw new DataNotFoundException();
        $discountedPriceData = $entity['discounted_price'] ?? null;

        $price = new Price(
            amount: $priceData['amount_minor'],
            currency: $priceData['currency'],
            formattedValue: $priceData['formatted_value'],
        );
        $affiliatePrice = $discountedPriceData ? new Price(
            amount: $discountedPriceData['amount_minor'],
            currency: $discountedPriceData['currency'],
            formattedValue: $discountedPriceData['formatted_value'],
        ) : null;

        $id = $entity['id'];

        return new Product(
            id: $id,
            name: $entity['name'],
            sku: $entity['sku'],
            slug: $entity['slug'] ?? throw new DataNotFoundException(),
            description: new ResolvedValue($entity['description'] ?? throw new DataNotFoundException()),
            shortDescription: new ResolvedValue($entity['short_description'] ?? throw new DataNotFoundException()),
            previewImageUrl: $entity['preview_image_url'],
            price: new ResolvedValue($price),
            biomarkers: new ExtraLazyPromise(fn() => $this->getBiomarkers($id)),
            shippingTypes: new ExtraLazyPromise(fn() => $this->getShippingTypes($id)),
            files: new ExtraLazyPromise(fn() => $this->getFiles($id)),
            categories: new ExtraLazyPromise(fn() => $this->getCategories($id)),
            faqs: new ExtraLazyPromise(fn() => $this->getFaqs($id)),
            video: new ExtraLazyPromise(fn() => $this->getVideo($id)),
            discountedPrice: new ResolvedValue($affiliatePrice),
        );
    }

    /**
     * @return PromiseInterface<list<Category>>
     */
    private function getCategories(string $id): PromiseInterface
    {
        $url = sprintf('/product/%s/categories', $id);

        return $this->get(ProductCategoryRepository::class)->findBy(url: $url);
    }

    /**
     * @return PromiseInterface<?Video>
     */
    private function getVideo(string $id): PromiseInterface
    {
        $url = sprintf('/product/%s/video', $id);

        return $this->get(VideoRepository::class)->findOneBy(url: $url);
    }

    /**
     * @return PromiseInterface<list<File>>
     */
    private function getFiles(string $id): PromiseInterface
    {
        $url = sprintf('/product/%s/files', $id);

        return $this->get(FileRepository::class)->findBy(url: $url);
    }

    /**
     * @return PromiseInterface<list<Biomarker>>
     */
    private function getBiomarkers(string $id): PromiseInterface
    {
        $url = sprintf('/product/%s/biomarkers', $id);

        return $this->get(BiomarkerRepository::class)->findBy(url: $url);
    }

    /**
     * @return PromiseInterface<list<Shipping>>
     */
    private function getShippingTypes(string $id): PromiseInterface
    {
        $url = sprintf('/product/%s/shipping', $id);

        return $this->get(ShippingRepository::class)->findBy(url: $url);
    }

    /**
     * @return PromiseInterface<list<ProductFaq>>
     */
    private function getFaqs(string $id): PromiseInterface
    {
        $url = sprintf('/product/%s/faqs', $id);

        return $this->get(ProductFaqRepository::class)->findBy(url: $url);
    }
}
