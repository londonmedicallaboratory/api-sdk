<?php

declare(strict_types=1);

namespace LML\SDK\Twig;

use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;

class Nl2BrNGExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('nl2br_ng', [$this, 'nl2brNextGen'], ['is_safe' => ['html']]),
        ];
    }

    public function nl2brNextGen(?string $input): string
    {
        if (!$input) {
            return '';
        }

        $input = strip_tags($input);
        $lined = preg_replace('/\v+|\\\r\\\n/', '<br/><br/>', $input);
        $lined = stripslashes($lined);

        return str_replace("\\n", '', $lined);
    }
}