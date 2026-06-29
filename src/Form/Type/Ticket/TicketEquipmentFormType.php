<?php

namespace App\Form\Type\Ticket;

use App\Entity\TicketEquipment;
use App\Enum\TicketEquipmentStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Security\Core\Security;
use App\Entity\EquipmentModel;
use App\Entity\EquipmentAsset;

class TicketEquipmentFormType extends AbstractType
{
    public function __construct(private Security $security) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();

        $builder
            ->add('equipmentModel', EntityType::class, [
                'class' => EquipmentModel::class,
                'choice_label' => 'name',
                'placeholder' => 'Select equipment',
                'choice_loader' => null,
                'attr' => [
                    'class' => 'form-select',
                    'class' => 'form-select js-select2-ajax',
                    'data-type' => 'equipment',
                ],
            ])
            ->add('comment', TextareaType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'attr' => ['class' => 'form-control mb-2', 'rows' => 3]
            ]);
            if ($this->security->isGranted('ROLE_MANAGER') || $this->security->isGranted('ROLE_ADMIN')) {
                $builder->add('equipmentAsset', EntityType::class, [
                    'class' => EquipmentAsset::class,
                    'choice_label' => 'reference',
                    'placeholder' => 'Select equipment',
                    'choice_loader' => null,
                    'attr' => [
                        'class' => 'form-select',
                        'class' => 'form-select js-select2-ajax',
                        'data-type' => 'equipmentAsset',
                    ],
                ])
                ->add('status', ChoiceType::class, [
                    'choices' => array_combine(
                        array_map(
                            fn (TicketEquipmentStatus $status) => 'ticket.enum.' . $status->value,
                            TicketEquipmentStatus::cases()
                        ),
                        TicketEquipmentStatus::cases()
                    ),
                    'choice_value' => fn (?TicketEquipmentStatus $choice) => $choice?->value,
                    'attr' => ['class' => 'form-select'],
                ]);
            }
    }
}