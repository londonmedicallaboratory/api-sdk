<?php

declare(strict_types=1);

namespace LML\SDK\Form\Type;

use Webmozart\Assert\Assert;
use LML\SDK\Entity\Address\Address;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * @extends AbstractType<Address>
 */
class AddressType extends AbstractType
{
    public function __construct(
        private string $loqateApiKey,
    )
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'show_company' => false,
            'factory' => $this->factory(...),
            'limit_countries' => true,
            'line1_placeholder' => null,
        ]);
        $resolver->setAllowedTypes('line1_placeholder', ['string', 'null']);
        $resolver->setAllowedTypes('limit_countries', 'bool');
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        Assert::nullOrString($line1Placeholder = $options['line1_placeholder']);

        $line1Attrs = [];
        if ($line1Placeholder) {
            $line1Attrs['placeholder'] = $line1Placeholder;
        }
        $builder->add('line1', TextType::class, [
            'required' => true,
            'get_value' => fn(Address $address) => $address->getAddressLine1(),
            'update_value' => fn(string $line, Address $address) => $address->setLine1($line),
            'constraints' => [
                new NotNull(),
            ],
            'attr' => $line1Attrs,
        ]);

        $builder->add('line2', TextType::class, [
            'required' => false,
            'get_value' => fn(Address $address) => $address->getAddressLine2(),
            'update_value' => fn(?string $line, Address $address) => $address->setLine2($line),
        ]);

        $builder->add('line3', TextType::class, [
            'required' => false,
            'get_value' => fn(Address $address) => $address->getAddressLine3(),
            'update_value' => fn(?string $line, Address $address) => $address->setLine3($line),
        ]);

        $builder->add('city', TextType::class, [
            'required' => true,
            'get_value' => fn(Address $address) => $address->getCity(),
            'update_value' => fn(string $city, Address $address) => $address->setCity($city),
            'constraints' => [
                new NotNull(),
            ],
        ]);

        $builder->add('postalCode', TextType::class, [
            'required' => true,
            'get_value' => fn(Address $address) => $address->getPostalCode(),
            'update_value' => fn(string $postalCode, Address $address) => $address->setPostalCode($postalCode),
            'constraints' => [
                new NotNull(),
            ],
        ]);

        Assert::boolean($limitCountries = $options['limit_countries']);
        $builder->add('countryCode', CountryType::class, [
            'limit_countries' => $limitCountries,
            'label' => 'Country',
            'get_value' => fn(Address $address) => $address->getCountryCode(),
            'update_value' => fn(string $country, Address $address) => $address->setCountryCode($country),
        ]);

        Assert::boolean($showCompany = $options['show_company'] ?? null);
        if ($showCompany) {
            $builder->add('company', TextType::class, [
                'get_value' => fn(Address $address) => $address->getCompany(),
                'update_value' => fn(?string $company, Address $address) => $address->setCompany($company),
                'constraints' => [
                    new Length(max: 35),
                ],
            ]);
        }
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['api_key'] = $this->loqateApiKey;
        $view->vars['is_populated'] = (bool)($form->get('line1')->getData());
    }

    public function getBlockPrefix(): string
    {
        return 'lml_sdk_address';
    }

    private function factory(string $line1, ?string $line2, ?string $line3, string $countryCode, string $postalCode, string $city): Address
    {
        return new Address(
            id: '',
            line1: $line1,
            line2: $line2,
            line3: $line3,
            countryName: 'UK',
            countryCode: $countryCode,
            postalCode: $postalCode,
            city: $city,
        );
    }
}
