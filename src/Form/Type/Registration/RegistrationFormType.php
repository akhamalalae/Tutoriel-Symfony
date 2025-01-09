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

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $view = $options['view'];

        $builder
            ->add('firstName',TextType::class,[
                'label' => 'First name',
                'attr' => ['class' => 'form-control'],
                'required' => true,
            ])
            ->add('name',TextType::class,[
                'label' => 'Name',
                'attr' => ['class' => 'form-control'],
                'required' => true,
            ])
            ->add('email',TextType::class,[
                'label' => 'Email',
                'attr' => ['class' => 'form-control'],
                'required' => true,
            ])
            ->add('dateOfBirth', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date of birth',
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
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
                    'attr' => ['class' => 'form-control'],
                    'required' => false,
                ])
                ->add('job',TextType::class,[
                    'label' => 'Job',
                    'attr' => ['class' => 'form-control'],
                    'required' => false,
                ])
                ->add('street', TextType::class, [
                    'label' => 'Rue',
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Ex. 123 Rue Principale',
                        'class' => 'address-autocomplete', // Classe pour JS
                    ],
                ])
                ->add('city', TextType::class, [
                    'label' => 'Ville',
                    'required' => false,
                    'attr' => ['placeholder' => 'Ex. Paris'],
                ])
                ->add('postal_code', TextType::class, [
                    'label' => 'Code Postal',
                    'required' => false,
                    'attr' => ['placeholder' => 'Ex. 75000'],
                ])
                ->add('country', TextType::class, [
                    'label' => 'Pays',
                    'required' => false,
                    'attr' => ['placeholder' => 'Ex. France'],
                ])
                ->add('twitter', TextType::class, [
                    'label' => 'Twitter',
                    'required' => false,
                    'attr' => ['placeholder' => 'Ex. Twitter'],
                ])
                ->add('facebook', TextType::class, [
                    'label' => 'Facebook',
                    'required' => false,
                    'attr' => ['placeholder' => 'Ex. Facebook'],
                ])
                ->add('instagram', TextType::class, [
                    'label' => 'Instagram',
                    'required' => false,
                    'attr' => ['placeholder' => 'Ex. Instagram'],
                ])
                ->add('linkedIn', TextType::class, [
                    'label' => 'LinkedIn',
                    'required' => false,
                    'attr' => ['placeholder' => 'Ex. LinkedIn'],
                ])
                ->add('skills', TextareaType::class, [
                    'label' => 'Skills',
                    'required' => false,
                    'attr' => ['placeholder' => 'Ex. HTML, JavaScript'],
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
