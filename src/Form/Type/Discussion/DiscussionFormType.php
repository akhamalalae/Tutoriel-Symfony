<?php

namespace App\Form\Type\Discussion;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Security;
use App\Entity\User;

class DiscussionFormType extends AbstractType
{
    public function __construct(
        private Security $security
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->security->getUser();
        
        $builder
            ->add('personInvitationRecipient', EntityType::class, array(
                'label' => false,
                'class' => User::class,
                'placeholder' => 'Choose the Recipient person',
                'required' => true,
                'choice_label' => function (User $user) {
                    return $user->getSensitiveDataFirstName() . ' ' . $user->getSensitiveDataName();
                },
                'attr' => [
                    'class' => 'form-control form-control-lg select2'
                ],
                'query_builder' => function (EntityRepository $er) use ($user) {
                    return $er->findUsersDiscussionForm($user);
                },
            ))
            ->add('save', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-lg btn-primary'
                ],
                'label' => 'Send'
            ]);
    }
}