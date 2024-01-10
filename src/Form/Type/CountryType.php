<?php

declare(strict_types=1);

namespace LML\SDK\Form\Type;

use Closure;
use Webmozart\Assert\Assert;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CountryType as BaseCountryType;
use function in_array;

/**
 * @extends AbstractType<string>
 */
class CountryType extends AbstractType
{
    public function getParent(): string
    {
        return BaseCountryType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'lml_sdk_country';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'limit_countries' => true,
        ]);
        $resolver->setAllowedTypes('limit_countries', 'bool');

        $resolver->addNormalizer('choice_filter', function (Options $options, ?Closure $default) {
            Assert::boolean($limitCountries = $options['limit_countries']);
            if (!$limitCountries) {
                return $default;
            }

            $allowed = ['GB'];

            return fn(?string $countryCode) => $countryCode && in_array($countryCode, $allowed, true);
        });

        $resolver->addNormalizer('preferred_choices', function (Options $options, array $default) {
            Assert::boolean($limitCountries = $options['limit_countries']);

            return $limitCountries ? $default : ['GB'];
        });
    }
}
