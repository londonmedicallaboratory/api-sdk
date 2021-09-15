<?php

declare(strict_types=1);

namespace LML\SDK\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use function in_array;

class CountryTypeExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        yield CountryType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $allowed = ['GB', 'RS', 'DE', 'IT'];
        $resolver->setDefault('choice_filter', fn(?string $countryCode) => $countryCode && in_array($countryCode, $allowed, true));
    }
}
