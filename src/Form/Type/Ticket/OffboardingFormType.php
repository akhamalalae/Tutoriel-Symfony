<?php

namespace App\Form\Type\Ticket;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use App\Entity\Offboarding;
use App\Entity\User;

class OffboardingFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('endDate', DateTimeType::class, [
                'widget' => 'single_text',   // permet <input type="datetime-local">
                'html5' => true,             // support HTML5
                'required' => false,
                'label' => 'ticket.field.offboarding.endDate',
                'attr' => [
                    'class' => 'form-control',
                ],
                'format' => 'yyyy-MM-dd\'T\'HH:mm:ss', // format compatible <input type="datetime-local">
            ])
            ->add('revokeAccess', CheckboxType::class, [
                'attr' => ['class' => 'form-control mb-2'],
                'label' => false,
                'required' => false,
            ])
            ->add('returnEquipment', CheckboxType::class, [
                'attr' => ['class' => 'form-control mb-2'],
                'label' => false,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offboarding::class,
        ]);
    }
}