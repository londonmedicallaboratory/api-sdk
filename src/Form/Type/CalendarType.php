<?php

declare(strict_types=1);

namespace LML\SDK\Form\Type;

use DateTimeInterface;
use Webmozart\Assert\Assert;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

/**
 * @extends AbstractType<DateTimeInterface>
 */
class CalendarType extends AbstractType
{
    public function getParent(): string
    {
        return DateTimeType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('calendar_url');
        $resolver->setRequired('daily_slots_url');
        $resolver->setDefaults([
            'input' => 'datetime_immutable',
            'widget' => 'single_text',
            'html5' => false,
            'input_format' => 'Y-m-d H:i',
        ]);

        $resolver->setDefault('auto_open', false);
        $resolver->setDefault('close_on_select', true);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['calendar_url'] = $options['calendar_url'];
        $view->vars['daily_slots_url'] = $options['daily_slots_url'];
        Assert::nullOrIsInstanceOf($data = $form->getData(), DateTimeInterface::class);
        $view->vars['formatted_time'] = $data?->format('Y-m-d H:i:s');
        $view->vars['pretty_time'] = $data?->format('M, jS Y H:i');
        $view->vars['auto_open'] = $options['auto_open'];
        $view->vars['close_on_select'] = $options['close_on_select'];
    }

    public function getBlockPrefix(): string
    {
        return 'lml_sdk_calender';
    }
}
