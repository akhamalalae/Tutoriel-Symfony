<?php

namespace App\Form\Type\Registration;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateType;


class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $view = $options['view'];

        $user = $builder->getData();

        $builder
            ->add('firstName',TextType::class,[
                'label' => 'First name',
                'data' => $user ? $user->getSensitiveDataFirstName() : '',
                'attr' => ['class' => 'form-control'],
                'required' => $view == 'update' ? false : true,
            ])
            ->add('name',TextType::class,[
                'label' => 'Name',
                'data' => $user ? $user->getSensitiveDataName() : '',
                'attr' => ['class' => 'form-control'],
                'required' => $view == 'update' ? false : true,
            ])
            ->add('image', FileType::class, [
                'label' => 'Image',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using attributes
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/*',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image',
                    ])
                ],
            ])
        ;

        if ($view == 'update') {
            $builder
                ->add('company',TextType::class,[
                    'label' => 'Company',
                    'data' => $user->getSensitiveDataCompany(),
                    'attr' => ['class' => 'form-control'],
                    'required' => false,
                ])
                ->add('job',TextType::class,[
                    'label' => 'Job',
                    'data' => $user->getSensitiveDataJob(),
                    'attr' => ['class' => 'form-control'],
                    'required' => false,
                ])
                ->add('street', TextType::class, [
                    'label' => 'Rue',
                    'required' => false,
                    'data' => $user->getSensitiveDataStreet(),
                    'attr' => [
                        'placeholder' => 'Ex. 123 Rue Principale',
                        'class' => 'address-autocomplete', // Classe pour JS
                    ],
                ])
                ->add('city', TextType::class, [
                    'label' => 'Ville',
                    'data' => $user->getSensitiveDataCity(),
                    'required' => false,
                    'attr' => ['placeholder' => 'Ex. Paris'],
                ])
                ->add('postal_code', TextType::class, [
                    'label' => 'Code Postal',
                    'data' => $user->getSensitiveDataPostalCode(),
                    'required' => false,
                    'attr' => ['placeholder' => 'Ex. 75000'],
                ])
                ->add('country', TextType::class, [
                    'label' => 'Pays',
                    'data' => $user->getSensitiveDataCountry(),
                    'required' => false,
                    'attr' => ['placeholder' => 'Ex. France'],
                ])
                ->add('twitter', TextType::class, [
                    'data' => $user->getSensitiveDataTwitter(),
                    'label' => 'Twitter',
                    'required' => false,
                    'attr' => ['placeholder' => 'Ex. Twitter'],
                ])
                ->add('facebook', TextType::class, [
                    'data' => $user->getSensitiveDataFacebook(),
                    'label' => 'Facebook',
                    'required' => false,
                    'attr' => ['placeholder' => 'Ex. Facebook'],
                ])
                ->add('instagram', TextType::class, [
                    'data' => $user->getSensitiveDataInstagram(),
                    'label' => 'Instagram',
                    'required' => false,
                    'attr' => ['placeholder' => 'Ex. Instagram'],
                ])
                ->add('linkedIn', TextType::class, [
                    'data' => $user->getSensitiveDataLinkedIn(),
                    'label' => 'LinkedIn',
                    'required' => false,
                    'attr' => ['placeholder' => 'Ex. LinkedIn'],
                ])
                ->add('skills', TextareaType::class, [
                    'label' => 'Skills',
                    'required' => false,
                    'attr' => ['placeholder' => 'Ex. HTML, JavaScript'],
                ])
                ->add('dateOfBirth', DateType::class, [
                    'label' => 'Date of birth',
                    'widget' => 'single_text',
                    'html5' => false,
                    'required' => false,
                    'attr' => ['class' => 'datepicker']
                ])
            ;
        }

        if ($view == 'register') {
            $builder->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('email',TextType::class,[
                'label' => 'Email',
                'attr' => ['class' => 'form-control'],
                'required' => true,
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'required' => true,
                'attr' => ['autocomplete' => 'new-password'],
                'attr' => ['class' => 'form-control form-control-lg'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'view' => '',
        ]);
    }
}
