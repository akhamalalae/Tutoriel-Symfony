<?php

namespace App\Controller\Messaging;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Type\Message\DiscussionFormType;
use App\Security\Messaging\DiscussionService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\Message;
use App\Entity\Discussion;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;

class DiscussionController extends AbstractController
{
    #[Route('/discussion', name: 'app_discussion')]
    public function create(
        Request $request,
        RequestStack $requestStack, 
        DiscussionService $discussionService, 
        EntityManagerInterface $em,
        Security $security,
        ) : Response
    {        
        $user = $security->getUser();

        $request = $requestStack->getMainRequest();

        $discussion = new Discussion();

        $discussionForm = $this->createForm(DiscussionFormType::class, $discussion);

        $discussionForm->handleRequest($request);

        $discussions = $em->getRepository(Discussion::class)->findDiscussion($user);

        if ($discussionForm->isSubmitted() && $discussionForm->isValid()) {
            return $discussionService->handleDiscussionFormData($discussionForm);
        }

        return $this->render('message/create.html.twig', [
            'formDiscussion' => $discussionForm->createView(),
            'discussions' => $discussions,
        ]);
    }
}
