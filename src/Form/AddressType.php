<?php

declare(strict_types=1);

namespace LML\SDK\Form;

use LML\SDK\Entity\Address\Address;
use Symfony\Component\Form\AbstractType;
use LML\SDK\Entity\Address\AddressInterface;
use LML\SDK\Form\Extension\CountryTypeLimited;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * @extends AbstractType<AddressInterface>
 */
class AddressType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'factory' => fn(
                string $line1,
                ?string $line2,
                ?string $line3,
                string $country,
                string $postalCode,
                string $city,
            ) => new Address(
                id: '',
                line1: $line1,
                line2: $line2,
                line3: $line3,
                countryName: 'UK',
                countryCode: $country,
                postalCode: $postalCode,
                city: $city,
            ),
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('line1', TextType::class, [
            'required' => true,
            'get_value' => fn(Address $address) => $address->getAddressLine1(),
            'update_value' => fn(string $line, Address $address) => $address->setLine1($line),
            'constraints' => [
                new NotNull(),
            ],
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

        $builder->add('country', CountryTypeLimited::class, [
            'get_value' => fn(Address $address) => $address->getCountryCode(),
            'update_value' => fn(string $country, Address $address) => $address->setCountryCode($country),
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'lml_sdk_address';
    }
}
