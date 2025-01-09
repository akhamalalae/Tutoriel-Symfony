<?php

namespace App\Form\Type\Discussion;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Security;
use App\Entity\User;
use App\Controller\Search\Discussion\SearchDiscussions;

class DiscussionFormType extends AbstractType
{
    public function __construct(
        private Security $security,
        private SearchDiscussions $searchDiscussions, 
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->security->getUser();
        
        $discussions = $this->searchDiscussions->findDiscussions($user)['discussions'];

        $builder
            ->add('personTwo', EntityType::class, array(
                'label' => false,
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return $user->getFirstName() . ' ' . $user->getName();
                },
                'query_builder' => function (EntityRepository $er) use ($user, $discussions) {
                    return $er->findUsersDiscussionForm($user, $discussions);
                },
            ))
            ->add('save', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                ],
                'label' => 'Send'
            ]);
    }
}