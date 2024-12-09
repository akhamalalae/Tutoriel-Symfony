<?php

namespace App\Controller\Messaging;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Type\Message\MessageFormType;
use App\Form\Type\Message\DiscussionFormType;
use App\Repository\MessageRepository;
use App\Controller\Messaging\DiscussionService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Message;
use App\Entity\Discussion;
use Symfony\Component\Security\Core\Security;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class DiscussionController extends AbstractController
{
    #[Route('/discussion', name: 'app_discussion')]
    public function index(
        Request $request,
        RequestStack $requestStack, 
        DiscussionService $discussionService, 
        EntityManagerInterface $em,
        Security $security)
    {        
        $user = $security->getUser();
        $request = $requestStack->getMainRequest();
        $discussion = new Discussion();

        $discussionForm = $this->createForm(DiscussionFormType::class, $discussion);

        $discussionForm->handleRequest($request);

        $listDiscussions = $em->getRepository(Discussion::class)->findDiscussion($user);

        if ($discussionForm->isSubmitted() && $discussionForm->isValid()) {
            return $discussionService->handleDiscussionFormData($discussionForm);
        }

        return $this->render('message/create.html.twig', [
            'formDiscussion' => $discussionForm->createView(),
            'listDiscussions' => $listDiscussions,
        ]);
    }
}
