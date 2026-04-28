<?php

namespace App\Form\Type\Ticket;

use App\Entity\SoftwareLicense;
use App\Entity\SoftwareProduct;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SoftwareLicenseFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('licenseKey', TextType::class, [
                'label' => 'Clé de licence',
                'required' => false,
                'attr' => [
                    'placeholder' => 'XXXX-XXXX-XXXX-XXXX'
                ]
            ])
            ->add('expiresAt', DateTimeType::class, [
                'label' => 'Date d’expiration',
                'required' => false,
                'widget' => 'single_text'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SoftwareLicense::class,
        ]);
    }
}