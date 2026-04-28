<?php

namespace App\Form\Type\Ticket;

use App\Entity\Onboarding;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class OnboardingFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('position', TextType::class, [
                'label' => 'ticket.field.onboarding.position',
                'required' => false,
                'attr' => ['class' => 'form-control mb-2'],
            ])
            ->add('startDate', DateTimeType::class, [
                'widget' => 'single_text',   // permet <input type="datetime-local">
                'html5' => true,             // support HTML5
                'required' => false,
                'label' => 'ticket.field.onboarding.startDate',
                'attr' => [
                    'class' => 'form-control',
                ],
                'format' => 'yyyy-MM-dd\'T\'HH:mm:ss', // format compatible <input type="datetime-local">
            ]);
    }
}