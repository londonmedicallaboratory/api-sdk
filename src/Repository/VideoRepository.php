<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\SDK\Entity\File\Video;
use LML\View\Lazy\ResolvedValue;
use LML\SDK\Entity\File\VideoInterface;
use LML\SDK\Service\API\AbstractRepository;

/**
 * @psalm-import-type S from VideoInterface
 *
 * @extends AbstractRepository<S, Video, array>
 */
class VideoRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer): Video
    {
        $id = $entity['id'];

        return new Video(
            id: $id,
            embedHtml: new ResolvedValue($entity['embed_html']),
            previewImageUrl: new ResolvedValue($entity['preview_image_url']),
        );
    }
}
