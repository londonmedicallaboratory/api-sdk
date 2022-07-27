<?php
declare(strict_types=1);

namespace LML\SDK\Entity\File;

use LML\SDK\Entity\ModelInterface;

/**
 * @psalm-type S=array{
 *      id: string,
 *      embed_html: string,
 *      preview_image_url: string,
 * }
 *
 * @extends ModelInterface<S>
 */
interface VideoInterface extends ModelInterface
{
    public function getEmbedHtml(): string;

    public function getPreviewImageUrl(): string;
}
