<?php

namespace App\Form\Type\Ticket;

use App\Entity\Ticket;
use App\Entity\User;
use App\Entity\Company;
use App\Entity\EquipmentModel;
use App\Enum\TicketStatus;
use App\Enum\TicketType;
use App\Enum\TicketPriority;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType; 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use App\Form\Type\Ticket\OnboardingFormType;
use App\Form\Type\Ticket\TicketSoftwareFormType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use App\Form\Type\Ticket\TicketEquipmentFormType;
use App\Form\Type\Ticket\OffboardingFormType;

class TicketFormType extends AbstractType
{
    public function __construct(private Security $security) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();

        $choices = [
            'ticket.enum.incident' => TicketType::INCIDENT,
            'ticket.enum.request' => TicketType::REQUEST,
        ];
    
        if ($this->security->isGranted('ROLE_MANAGER') || $this->security->isGranted('ROLE_ADMIN')) {
            $choices['ticket.enum.onboarding'] = TicketType::ONBOARDING;
            $choices['ticket.enum.offboarding'] = TicketType::OFFBOARDING;
        }

        $builder
            ->add('title', TextType::class, [
                'label' => 'ticket.field.title',
                'required' => true,
                'constraints' => [new NotBlank(['message' => 'Title is required'])],
                'attr' => ['class' => 'form-control mb-2'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'ticket.field.description',
                'required' => true,
                'constraints' => [new NotBlank(['message' => 'Description is required'])],
                'attr' => ['class' => 'form-control mb-2', 'rows' => 4],
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'ticket.field.type',
                'choices' => $choices,
                'choice_value' => fn (?TicketType $choice) => $choice?->value,
                'required' => true,
                'attr' => ['class' => 'form-control mb-2', 'id' => 'ticket_type'],
            ])
            ->add('priority', ChoiceType::class, [
                'label' => 'ticket.field.priority',
                'choices' => array_combine(
                    array_map(
                        fn (TicketPriority $priority) => 'ticket.enum.' . $priority->value,
                        TicketPriority::cases()
                    ),
                    TicketPriority::cases()
                ),
                'choice_value' => fn (?TicketPriority $choice) => $choice?->value,
                'required' => true,
                'attr' => ['class' => 'form-control mb-2'],
            ])
            ->add('company', EntityType::class, [
                'label' => 'ticket.field.company',
                'class' => Company::class,
                'choice_label' => 'name',
                'required' => true,
                'attr' => ['class' => 'form-control mb-2'],
            ])
            ->add('targetUser', EntityType::class, [
                'label' => 'ticket.field.target_user',
                'class' => User::class,
                'choice_label' => 'sensitiveDataName',
                'required' => false,
                'choices' => [],
                'attr' => [
                    'class' => 'form-select js-select2-ajax',
                    'data-type' => 'user',
                    'data-multiple' => 'false',
                ],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control mb-2',
                    'rows' => 3,
                ],
            ])
            ->add('ticketEquipment', CollectionType::class, [
                'entry_type' => TicketEquipmentFormType::class,
                'allow_add' => true,
                'label' => false,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
            ])
            ->add('ticketSoftware', CollectionType::class, [
                'entry_type' => TicketSoftwareFormType::class,
                'allow_add' => true,
                'label' => false,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
            ]);
            if ($this->security->isGranted('ROLE_MANAGER') || $this->security->isGranted('ROLE_ADMIN')) {
                $builder->add('status', ChoiceType::class, [
                    'label' => 'ticket.field.status',
                    'choices' => array_combine(
                        array_map(
                            fn (TicketStatus $status) => 'ticket.enum.' . $status->value,
                            TicketStatus::cases()
                        ),
                        TicketStatus::cases()
                    ),
                    'choice_value' => fn (?TicketStatus $choice) => $choice?->value,
                    'required' => false,
                    'attr' => ['class' => 'form-control mb-2'],
                ])
                ->add('assignedTo', EntityType::class, [
                    'label' => 'ticket.field.assigned_to',
                    'class' => User::class,
                    'choice_label' => 'sensitiveDataName',
                    'required' => false,
                    'choices' => [],
                    'attr' => [
                        'class' => 'form-select js-select2-ajax',
                        'data-type' => 'user',
                        'data-multiple' => 'false',
                    ],
                ])
                ->add('onboarding', OnboardingFormType::class, [
                    'required' => false,
                ])
                ->add('offboarding', OffboardingFormType::class, [
                    'required' => false,
                ]);
            }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}