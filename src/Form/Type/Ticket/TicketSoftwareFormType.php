<?php

namespace App\Form\Type\Ticket;

use App\Entity\SoftwareLicense;
use App\Entity\SoftwareProduct;
use App\Entity\TicketSoftware;
use App\Entity\User;
use App\Enum\TicketSoftwareStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Form\Type\Ticket\SoftwareLicenseFormType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class TicketSoftwareFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('softwareLicense', SoftwareLicenseFormType::class, [
                'required' => false,
            ])
            ->add('softwareProduct', EntityType::class, [
                'label' => 'SoftwareProduct',
                'class' => SoftwareProduct::class,
                'choice_label' => 'name',
                'required' => true,
                'attr' => ['class' => 'form-control mb-2'],
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'ticket.enum.pending' => TicketSoftwareStatus::PENDING,
                    'ticket.enum.preparing' => TicketSoftwareStatus::PREPARING,
                    'ticket.enum.ready' => TicketSoftwareStatus::READY,
                    'ticket.enum.delivered' => TicketSoftwareStatus::DELIVERED,
                    'ticket.enum.installed' => TicketSoftwareStatus::INSTALLED,
                ],
                'choice_value' => fn (?TicketSoftwareStatus $choice) => $choice?->value,
                'attr' => ['class' => 'form-select'],
            ])
            ->add('comment', TextareaType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'attr' => ['class' => 'form-control mb-2', 'rows' => 3]
            ]);
    }
}