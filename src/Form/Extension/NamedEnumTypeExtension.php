<?php

declare(strict_types=1);

namespace LML\SDK\Form\Extension;

use Closure;
use LML\SDK\Enum\Model\NameableInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

/**
 * If enum implements @see NameableInterface , use that to generate `choice_label` values.
 * This avoids lots of repetitions.
 */
class NamedEnumTypeExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        yield EnumType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->addNormalizer('choice_label', function (Options $options, Closure $default) {

            /** @var class-string $class */
            $class = $options['class'];
            if (is_a($class, NameableInterface::class, true)) {
                return fn(NameableInterface $nameable) => $nameable->getName();
            }

            return $default;
        });
    }
}
