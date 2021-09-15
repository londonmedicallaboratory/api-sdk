<?php

declare(strict_types=1);

namespace LML\SDK\Form;

use DateTime;
use LML\SDK\Enum\GenderEnum;
use LML\SDK\Enum\EthnicityEnum;
use LML\View\Lazy\ResolvedValue;
use LML\SDK\Model\Address\Address;
use Symfony\Component\Form\AbstractType;
use LML\SDK\Repository\ProductRepository;
use LML\SDK\Model\Product\ProductInterface;
use LML\SDK\Model\Address\AddressInterface;
use Symfony\Component\Form\FormBuilderInterface;
use LML\SDK\Model\TestRegistration\TestRegistration;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;

/**
 * @extends AbstractType<TestRegistration>
 */
class TestRegistrationType extends AbstractType
{
    public function __construct(
        private ProductRepository $productRepository,
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('product', ChoiceType::class, [
            'choices'      => $this->productRepository->findAll(true),
            'choice_label' => fn(ProductInterface $product) => $product->getName(),
            'get_value'    => fn(TestRegistration $registration) => $registration->getProduct(),
            'update_value' => fn(ProductInterface $product, TestRegistration $registration) => $registration->setProduct($product),
        ]);

        $builder->add('email', EmailType::class, [
            'get_value'    => fn(TestRegistration $registration) => $registration->getEmail(),
            'update_value' => fn(string $email, TestRegistration $registration) => $registration->setEmail($email),
            'constraints'  => [
                new EmailConstraint(),
            ],
        ]);

        $builder->add('dateOfBirth', DateType::class, [
            'get_value'    => fn(TestRegistration $registration) => $registration->getDateOfBirth(),
            'update_value' => fn(DateTime $dateOfBirth, TestRegistration $registration) => $registration->setDateOfBirth($dateOfBirth),
        ]);

        $builder->add('firstName', TextType::class, [
            'get_value'    => fn(TestRegistration $registration) => $registration->getFirstName(),
            'update_value' => fn(string $name, TestRegistration $registration) => $registration->setFirstName($name),
        ]);

        $builder->add('lastName', TextType::class, [
            'get_value'    => fn(TestRegistration $registration) => $registration->getLastName(),
            'update_value' => fn(string $name, TestRegistration $registration) => $registration->setLastName($name),
        ]);

        $builder->add('gender', ChoiceType::class, [
            'choices'      => GenderEnum::getAsFormChoices(),
            'get_value'    => fn(TestRegistration $registration) => $registration->getGender(),
            'update_value' => /** @param GenderEnum::* $gender */ fn(string $gender, TestRegistration $registration) => $registration->setGender($gender),
        ]);

        $builder->add('ethnicity', ChoiceType::class, [
            'choices'      => EthnicityEnum::getAsFormGroupChoices(),
            'get_value'    => fn(TestRegistration $registration) => $registration->getEthnicity(),
            'update_value' => /** @param EthnicityEnum::* $ethnicity */ fn(string $ethnicity, TestRegistration $registration) => $registration->setEthnicity($ethnicity),
        ]);

        $builder->add('mobilePhoneNumber', TextType::class, [
            'get_value'    => fn(TestRegistration $registration) => $registration->getMobilePhoneNumber(),
            'update_value' => fn(string $number, TestRegistration $registration) => $registration->setMobilePhoneNumber($number),
        ]);

        $builder->add('passportNumber', TextType::class, [
            'get_value'    => fn(TestRegistration $registration) => $registration->getPassportNumber(),
            'update_value' => fn(string $number, TestRegistration $registration) => $registration->setPassportNumber($number),
        ]);

        $builder->add('nhsNumber', TextType::class, [
            'get_value'    => fn(TestRegistration $registration) => $registration->getNhsNumber(),
            'update_value' => fn(?string $number, TestRegistration $registration) => $registration->setNhsNumber($number),
        ]);

        $builder->add('isVaccinated', ChoiceType::class, [
            'placeholder'  => 'Select status',
            'choices'      => [
                'Vaccinated'     => true,
                'Not Vaccinated' => false,
            ],
            'get_value'    => fn(TestRegistration $registration) => $registration->isVaccinated(),
            'update_value' => fn(bool $isVaccinated, TestRegistration $registration) => $registration->setIsVaccinated($isVaccinated),
        ]);

        $builder->add('ukAddress', AddressType::class, [
            'get_value'    => fn(TestRegistration $registration) => $registration->getUkAddress(),
            'update_value' => fn(AddressInterface $address, TestRegistration $registration) => $registration->setUkAddress($address),
            'constraints'  => [
                new NotNull(message: 'You must create an address'),
            ],
        ]);

        $builder->add('selfIsolatingAddress', AddressType::class, [
            'get_value'    => fn(TestRegistration $registration) => $registration->getUkAddress(),
            'update_value' => fn(AddressInterface $address, TestRegistration $registration) => $registration->setUkAddress($address),
            'constraints'  => [
                new NotNull(message: 'You must create an address'),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('factory',
            /**
             * @param GenderEnum::* $gender
             * @param EthnicityEnum::* $ethnicity
             */
            fn(
                ProductInterface $product,
                string           $email,
                DateTime         $dateOfBirth,
                string           $firstName,
                string           $lastName,
                string           $gender,
                string           $ethnicity,
                string           $mobilePhoneNumber,
                string           $passportNumber,
                ?string          $nhsNumber,
                bool             $isVaccinated,
                Address          $ukAddress,
            ) => new TestRegistration(
                product: new ResolvedValue($product),
                email: $email,
                dateOfBirth: $dateOfBirth,
                firstName: $firstName,
                lastName: $lastName,
                gender: $gender,
                ethnicity: $ethnicity,
                mobilePhoneNumber: $mobilePhoneNumber,
                nhsNumber: $nhsNumber,
                isVaccinated: $isVaccinated,
                passportNumber: $passportNumber,
                ukAddress: new ResolvedValue($ukAddress),
            ));
    }
}
