<?php

namespace App\Form\Type\Stock;

use App\Entity\Company;
use App\Entity\User;
use App\Entity\EquipmentAsset;
use App\Enum\EquipmentStockStatus;
use App\Enum\ConditionEquipmentStock;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\{
    TextType, DateType, MoneyType, ChoiceType, TextareaType
};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EquipmentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

        // =========================
        // IDENTIFICATION
        // =========================
        ->add('reference', TextType::class, [
            'label' => 'ticket.field.equipments.reference'
        ])
        ->add('serialNumber', TextType::class, [
            'label' => 'ticket.field.equipments.N° Série'
        ])
        ->add('assetTag', TextType::class, [
            'label' => 'stock.form.fields.assetTag',
            'required' => false
        ])
        ->add('company', EntityType::class, [
            'label' => 'ticket.field.company',
            'class' => Company::class,
            'choice_label' => 'name',
            'required' => true,
            'attr' => ['class' => 'form-control mb-2'],
        ])

        // =========================
        // STATUT
        // =========================
        ->add('status', ChoiceType::class, [
            'label' => 'stock.form.fields.status',
            'choices' => [
                'stock.status.available' => EquipmentStockStatus::AVAILABLE,
                'stock.status.reserved' => EquipmentStockStatus::RESERVED,
                'stock.status.Assigned' => EquipmentStockStatus::ASSIGNED,
                'stock.status.in_repair' => EquipmentStockStatus::IN_REPAIR,
                'stock.status.retired' => EquipmentStockStatus::RETIRED,
                'stock.status.out_of_service' => EquipmentStockStatus::OUT_OF_SERVICE,
            ],
            'choice_label' => fn ($choice) => $choice->value,
        ])
        ->add('assetCondition', ChoiceType::class, [
            'label' => 'stock.form.fields.assetCondition',
            'choices' => [
                'stock.condition.new' => ConditionEquipmentStock::NEW,
                'stock.condition.good' => ConditionEquipmentStock::GOOD,
                'stock.condition.fair' => ConditionEquipmentStock::FAIR,
                'stock.condition.broken' => ConditionEquipmentStock::BROKEN,
            ],
            'choice_label' => fn ($choice) => $choice->value,
        ])

        // =========================
        // AFFECTATION
        // =========================
        ->add('assignedTo', EntityType::class, [
            'label' => 'ticket.field.assigned_to',
            'class' => User::class,
            'choice_label' => 'sensitiveDataName',
            'required' => false,
            'attr' => [
                'class' => 'form-select js-select2-ajax',
                'data-type' => 'user',
                'data-multiple' => 'false',
            ],
        ])

        // =========================
        // LOCALISATION
        // =========================
        ->add('location', TextType::class, [
            'label' => 'stock.form.fields.location',
            'required' => false
        ])
        ->add('storageLocation', TextType::class, [
            'label' => 'stock.form.fields.storageLocation',
            'required' => false
        ])

        // =========================
        // FINANCE
        // =========================
        ->add('purchaseDate', DateType::class, [
            'widget' => 'single_text',
            'label' => 'stock.form.fields.purchaseDate',
            'required' => false
        ])
        ->add('purchasePrice', MoneyType::class, [
            'label' => 'stock.form.fields.purchasePrice',
            'required' => false
        ])
        ->add('supplier', TextType::class, [
            'label' => 'stock.form.fields.supplier',
            'required' => false
        ])
        ->add('invoiceNumber', TextType::class, [
            'label' => 'stock.form.fields.invoiceNumber',
            'required' => false
        ])

        // =========================
        // MAINTENANCE
        // =========================
        ->add('warrantyEndDate', DateType::class, [
            'widget' => 'single_text',
            'label' => 'stock.form.fields.warrantyEndDate',
            'required' => false
        ])
        ->add('lastMaintenanceAt', DateType::class, [
            'widget' => 'single_text',
            'label' => 'stock.form.fields.lastMaintenanceAt',
            'required' => false
        ])
        ->add('maintenanceComment', TextareaType::class, [
            'label' => 'stock.form.fields.maintenanceComment',
            'required' => false
        ])

        // =========================
        // COMMENTAIRE
        // =========================
        ->add('comment', TextareaType::class, [
            'label' => 'stock.form.fields.comment',
            'required' => false
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EquipmentAsset::class,
        ]);
    }
}