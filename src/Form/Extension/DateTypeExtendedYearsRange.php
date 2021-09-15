<?php

declare(strict_types=1);

namespace LML\SDK\Form\Extension;

use DateTime;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use function range;

class DateTypeExtendedYearsRange extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        yield DateType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $now = new DateTime();
        $year = (int)$now->format('Y');

        $range = range($year - 80, $year);

        $resolver->setDefault('years', $range);
    }
}
