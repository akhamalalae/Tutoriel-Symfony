<?php

namespace App\Form\Type\User;

use App\Entity\User;
use App\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $view = $options['view'];

        $user = $builder->getData();

        $builder
            ->add('firstName',TextType::class,[
                'label' => 'user.first_name',
                'data' => $user ? $user->getSensitiveDataFirstName() : '',
                'attr' => ['class' => 'form-control'],
                'required' => $view == 'update' ? false : true,
            ])
            ->add('name',TextType::class,[
                'label' => 'user.last_name',
                'data' => $user ? $user->getSensitiveDataName() : '',
                'attr' => ['class' => 'form-control'],
                'required' => $view == 'update' ? false : true,
            ])
        ;

        if ($view == 'update' || $view == 'all') {
            $builder
                ->add('image', FileType::class, [
                    'label' => 'user.image',

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
                ->add('companyUser', EntityType::class, [
                    'label' => 'user.company',
                    'class' => Company::class,
                    'choice_label' => 'name',
                    'required' => $view == 'update' ? false : true,
                    'attr' => ['class' => 'form-control mb-2'],
                ])
                ->add('job',TextType::class,[
                    'label' => 'user.job',
                    'data' => $user->getSensitiveDataJob(),
                    'attr' => ['class' => 'form-control'],
                    'required' => false,
                ])
                ->add('street', TextType::class, [
                    'label' => 'user.street',
                    'required' => false,
                    'data' => $user->getSensitiveDataStreet(),
                    'attr' => [
                        'placeholder' => 'Ex. 123 Rue Principale',
                        'class' => 'address-autocomplete', // Classe pour JS
                    ],
                ])
                ->add('city', TextType::class, [
                    'label' => 'user.city',
                    'data' => $user->getSensitiveDataCity(),
                    'required' => false,
                    'attr' => ['placeholder' => 'Ex. Paris'],
                ])
                ->add('postal_code', TextType::class, [
                    'label' => 'user.postal_code',
                    'data' => $user->getSensitiveDataPostalCode(),
                    'required' => false,
                    'attr' => ['placeholder' => 'Ex. 75000'],
                ])
                ->add('country', TextType::class, [
                    'label' => 'user.country',
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
                    'label' => 'user.skills',
                    'required' => false,
                    'attr' => ['placeholder' => 'Ex. HTML, JavaScript'],
                ])
                ->add('dateOfBirth', DateType::class, [
                    'widget' => 'single_text',
                    'label' => 'user.date_of_birth',
                    'required' => false
                ])
            ;
        }

        if ($view == 'register' || $view == 'all') {
            $builder->add('email',TextType::class,[
                'label' => 'user.email',
                'data' => $user ? $user->getSensitiveDataEmail() : '',
                'attr' => ['class' => 'form-control'],
                'required' => true,
            ]);

            if ($view == 'all') {
                $builder->add('plainPassword', PasswordType::class, [
                    'label' => 'Password',
                    'mapped' => false,
                    'required' => false,
                ])
                ->add('roles', ChoiceType::class, [
                    'label' => 'user.role.title',
                    'choices' => [
                        'user.role.user' => 'ROLE_USER',
                        'user.role.manager' => 'ROLE_MANAGER',
                        'user.role.admin' => 'ROLE_ADMIN',
                    ],
                    'multiple' => true,   // IMPORTANT (Symfony attend un array)
                    'expanded' => true,   // checkbox UI
                    'required' => true,
                ]);
            } else {
                $builder->add('plainPassword', PasswordType::class, [
                    // instead of being set onto the object directly,
                    // this is read and encoded in the controller
                    'label' => 'Password',
                    'mapped' => false,
                    'required' => true,
                    'attr' => ['autocomplete' => 'new-password'],
                    'attr' => ['class' => 'form-control form-control-lg'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'user.error.password_required',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'user.error.password_min',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                ])
                ->add('agreeTerms', CheckboxType::class, [
                    'mapped' => false,
                    'required' => true,
                    'constraints' => [
                        new IsTrue([
                            'message' => 'user.error.terms',
                        ]),
                    ],
                ]);
            }
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
