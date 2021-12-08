<?php

declare(strict_types=1);

namespace LML\SDK\Form;

use DateTime;
use DateTimeInterface;
use LML\SDK\Enum\GenderEnum;
use LML\SDK\Enum\EthnicityEnum;
use Symfony\Component\Form\FormEvents;
use LML\SDK\Enum\VaccinationStatusEnum;
use Symfony\Component\Form\AbstractType;
use LML\SDK\Repository\ProductRepository;
use Symfony\Component\Form\FormInterface;
use LML\SDK\Model\Address\AddressInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Event\PreSubmitEvent;
use LML\SDK\Model\TestRegistration\TestRegistration;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\LessThan;
use LML\SDK\Form\Extension\DateTypeExtendedYearsRange;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;
use function range;

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

//    public function configureOptions(OptionsResolver $resolver): void
//    {
//        $resolver->setDefaults([
//            'show_factory_error' => true,
//        ]);
//        $resolver->setDefault('factory',
//            /**
//             * @param GenderEnum::* $gender
//             * @param null|EthnicityEnum::* $ethnicity
//             * @param VaccinationStatusEnum::* $vaccinationStatus
//             */
//            fn(
//                ProductInterface  $product,
//                string            $email,
//                DateTime          $dateOfBirth,
//                string            $firstName,
//                string            $lastName,
//                string            $gender,
//                ?string           $ethnicity,
//                string            $mobilePhoneNumber,
//                string            $passportNumber,
//                ?string           $nhsNumber,
//                string            $vaccinationStatus,
//                Address           $ukAddress,
//                DateTimeInterface $dateOfArrival,
//            ) => new TestRegistration(
//                product: new ResolvedValue($product),
//                email: $email,
//                dateOfBirth: $dateOfBirth,
//                firstName: $firstName,
//                lastName: $lastName,
//                gender: $gender,
//                ethnicity: $ethnicity,
//                mobilePhoneNumber: $mobilePhoneNumber,
//                nhsNumber: $nhsNumber,
//                vaccinationStatus: $vaccinationStatus,
//                passportNumber: $passportNumber,
//                ukAddress: new ResolvedValue($ukAddress),
//                dateOfArrival: $dateOfArrival,
//            ));
//    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
//        $builder->add('product', ChoiceType::class, [
//            'choices'      => $this->productRepository->findAll(true),
//            'choice_label' => fn(ProductInterface $product) => $product->getName(),
//            'get_value'    => fn(TestRegistration $registration) => $registration->getProduct(),
//            'update_value' => fn(ProductInterface $product, TestRegistration $registration) => $registration->setProduct($product),
//        ]);

        $builder->add('email', EmailType::class, [
            'get_value' => fn(TestRegistration $registration) => $registration->getEmail(),
            'update_value' => fn(string $email, TestRegistration $registration) => $registration->setEmail($email),
            'constraints' => [
                new EmailConstraint(),
            ],
        ]);

        $builder->add('dateOfBirth', DateTypeExtendedYearsRange::class, [
            'get_value' => fn(?TestRegistration $registration) => $registration ? $registration->getDateOfBirth() : new DateTime('2000-10-10'),
            'update_value' => fn(DateTime $dateOfBirth, TestRegistration $registration) => $registration->setDateOfBirth($dateOfBirth),
        ]);

        $builder->add('firstName', TextType::class, [
            'get_value' => fn(TestRegistration $registration) => $registration->getFirstName(),
            'update_value' => fn(string $name, TestRegistration $registration) => $registration->setFirstName($name),
        ]);

        $builder->add('lastName', TextType::class, [
            'get_value' => fn(TestRegistration $registration) => $registration->getLastName(),
            'update_value' => fn(string $name, TestRegistration $registration) => $registration->setLastName($name),
        ]);

        $builder->add('gender', ChoiceType::class, [
            'choices' => GenderEnum::getAsFormChoices(),
            'get_value' => fn(TestRegistration $registration) => $registration->getGender(),
            'update_value' => /** @param GenderEnum::* $gender */ fn(string $gender, TestRegistration $registration) => $registration->setGender($gender),
        ]);

        $builder->add('ethnicity', ChoiceType::class, [
            'choices' => EthnicityEnum::getAsFormGroupChoices(),
            'get_value' => fn(TestRegistration $registration) => $registration->getEthnicity(),
            'update_value' => /** @param null|EthnicityEnum::* $ethnicity */ fn(?string $ethnicity, TestRegistration $registration) => $registration->setEthnicity($ethnicity),
        ]);

        $builder->add('mobilePhoneNumber', TextType::class, [
            'get_value' => fn(TestRegistration $registration) => $registration->getMobilePhoneNumber(),
            'update_value' => fn(string $number, TestRegistration $registration) => $registration->setMobilePhoneNumber($number),
        ]);

        $builder->add('passportNumber', TextType::class, [
            'get_value' => fn(TestRegistration $registration) => $registration->getPassportNumber(),
            'update_value' => fn(string $number, TestRegistration $registration) => $registration->setPassportNumber($number),
        ]);

        $builder->add('nhsNumber', TextType::class, [
            'get_value' => fn(TestRegistration $registration) => $registration->getNhsNumber(),
            'update_value' => fn(?string $number, TestRegistration $registration) => $registration->setNhsNumber($number),
        ]);

        $builder->add('vaccinationStatus', ChoiceType::class, [
            'placeholder' => 'Select status',
            'choices' => [
                'Vaccinated' => VaccinationStatusEnum::VACCINATED,
                'Not Vaccinated' => VaccinationStatusEnum::NOT_VACCINATED,
            ],
            'get_value' => fn(TestRegistration $registration) => $registration->isVaccinated(),
            'update_value' => /** @param VaccinationStatusEnum::* $vaccinationStatus */ fn(string $vaccinationStatus, TestRegistration $registration) => $registration->setVaccinationStatus($vaccinationStatus),
        ]);

        $builder->add('ukAddress', AddressType::class, [
            'get_value' => fn(TestRegistration $registration) => $registration->getUkAddress(),
            'update_value' => fn(AddressInterface $address, TestRegistration $registration) => $registration->setUkAddress($address),
            'constraints' => [
                new NotNull(message: 'You must create an address'),
            ],
        ]);

        $builder->add('isSelfIsolating', ChoiceType::class, [
            'label' => 'Are you self-isolating at a different address?',
            'dynamic' => true,
            'expanded' => true,
            'choices' => [
                'No' => false,
                'Yes' => true,
            ],
            'get_value' => fn(?TestRegistration $registration) => $registration && $registration->getSelfIsolatingAddress(),
            'update_value' => fn() => null,
        ]);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (PreSubmitEvent $event) {
            /** @var array{isSelfIsolating?: '1'} $data */
            $data = $event->getData();
            $this->addSelfIsolatingAddressField($event->getForm(), isset($data['isSelfIsolating']) && $data['isSelfIsolating'] === '1');
        });

        // START: date-based fields
        $now = new DateTime();
        $year = (int)$now->format('Y');
        $builder->add('dateOfArrival', DateType::class, [
            'label' => 'Date of Arrival In The UK',
            'get_value' => fn(?TestRegistration $registration) => $registration ? $registration->getDayOfArrival() : $now,
            'update_value' => fn(DateTimeInterface $date, TestRegistration $registration) => $registration->setDateOfArrival($date),
            'years' => range($year, $year + 2),
            'constraints' => [
                new GreaterThan(value: $now),
            ],
        ]);

        $builder->add('nonExemptDay', DateType::class, [
            'required' => false,
            'placeholder' => '',
            'label' => 'Date on which you last departed from or transited through a country or territory outside the common travel area (optional)',
            'get_value' => fn(?TestRegistration $registration) => $registration?->getNonExemptDay(),
            'update_value' => fn(?DateTimeInterface $date, TestRegistration $registration) => $registration->setNonExemptDay($date),
            'years' => range($year - 1, $year),
            'constraints' => [
                new LessThan(value: $now),
            ],
        ]);
        // END: date-based fields

        $builder->add('travellingFromCountry', CountryType::class, [
            'label' => 'Country Travelling From',
            'placeholder' => 'Select country',
            'mapped' => false,
            'constraints' => [
                new NotNull(),
            ],
        ]);

        $builder->add('travelNumber', TextType::class, [
            'label' => 'Coach number, flight number or vessel name (as appropriate)',
            'mapped' => false,
        ]);

        $builder->add('transitCountries', CountryType::class, [
            'required' => false,
            'label' => 'any countries or territories you transited through as part of this journey (optional)',
            'multiple' => true,
            'get_value' => fn(?TestRegistration $registration) => $registration?->getTransitCountryCodes() ?? [],
            'add_value' => fn(string $code, TestRegistration $registration) => $registration->addTransitCountry($code),
            'remove_value' => fn(string $code, TestRegistration $registration) => $registration->removeTransitCountry($code),
        ]);
    }

    private function addSelfIsolatingAddressField(FormInterface $form, bool $add): void
    {
        if (!$add) {
            $form->remove('selfIsolatingAddress');

            return;
        }

        $form->add('selfIsolatingAddress', AddressType::class, [
            'get_value' => fn(TestRegistration $registration) => $registration->getSelfIsolatingAddress(),
            'update_value' => fn(?AddressInterface $address, TestRegistration $registration) => $registration->setSelfIsolatingAddress($address),
        ]);
    }
}
