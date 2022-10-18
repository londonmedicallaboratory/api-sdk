<?php

declare(strict_types=1);

namespace LML\SDK\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use function in_array;

class CountryTypeLimited extends AbstractType
{
    public function getParent(): string
    {
        return CountryType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $allowed = ['GB', 'RS', 'DE', 'IT'];
        $resolver->setDefault('choice_filter', fn(?string $countryCode) => $countryCode && in_array($countryCode, $allowed, true));
        $resolver->setDefault('preferred_choices', ['GB']);
    }
}
