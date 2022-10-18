<?php

declare(strict_types=1);

namespace LML\SDK\Form\Type;

use LML\SDK\Enum\EthnicityEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

/**
 * @extends AbstractType<EthnicityEnum>
 */
class EthnicityChoiceType extends AbstractType
{
    public function getParent(): string
    {
        return EnumType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => EthnicityEnum::class,
            'group_by' => fn(EthnicityEnum $enum) => $enum->getGroupName(),
        ]);
    }
}
