<?php

declare(strict_types=1);

namespace LML\SDK\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

/**
 * @extends AbstractType<void>
 */
class CalendarType extends AbstractType
{
    public function getBlockPrefix(): string
    {
        return 'lml_sdk_calender';
    }

    public function getParent(): string
    {
        return DateTimeType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('monthly_info_url');
        $resolver->setRequired('daily_info_url');
    }
}
