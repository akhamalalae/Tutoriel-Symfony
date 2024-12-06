<?php

namespace App\Form\Type\Search;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Form\Model\SearchModel;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use App\Entity\Category;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class SearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('query', SearchType::class,[
                'required' => false
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'required' => false
            ])
            ->add('createdThisMonth', CheckboxType::class,[
                'required' => false
            ]) 
            ->setMethod('GET');
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'allow_extra_fields' => true,
            'data_class' => SearchModel::class,
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
