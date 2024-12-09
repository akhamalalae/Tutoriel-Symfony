<?php

namespace App\Form\Type\Message;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Security;

class DiscussionFormType extends AbstractType
{
    public function __construct(
        private Security $security,
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->security->getUser()?->getId();

        $builder
            ->add('personTwo', EntityType::class, array(
                'label' => false,
                'class' => User::class,
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $er) use ($user) {
                    return $er->createQueryBuilder('u')
                    //->innerJoin('u.user', 'user')
                    ->where('u.id != :user')
                    ->setParameter('user', $user)
                    ;
                },
            ))
            ->add('save', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                ],
                'label' => 'Go'
            ]);
    }
}