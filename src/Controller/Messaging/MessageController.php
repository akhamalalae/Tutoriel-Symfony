<?php

namespace App\Controller\Messaging;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Type\Message\MessageFormType;
use App\Security\Messaging\MessageService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Message;
use App\Entity\Discussion;
use Symfony\Component\Security\Core\Security;
use App\Entity\User;
use Twig\Environment;

class MessageController extends AbstractController
{
    #[Route('/message/{idDiscussion}/{page}', name: 'app_create_message')]
    public function create(
        Request $request,
        RequestStack $requestStack, 
        MessageService $messageService, 
        Environment $environment,
        EntityManagerInterface $em,
        Security $security,
        int $idDiscussion = 0,
        int $page = 0) : JsonResponse
    {    
        $message = new Message();

        $idDiscussion = $request->get('idDiscussion'); 

        $page = $request->get('page'); 

        $discussion = $em->getRepository(Discussion::class)->find($idDiscussion); 

        $user = $security->getUser();

        $this->setDiscussioReadingMessageStatus($em, $discussion, $user);

        $messagesPaginationInfos = $messageService->messagesPaginationInfos($user, $discussion, $page);

        $request = $requestStack->getMainRequest();

        $messageForm = $this->createForm(MessageFormType::class, $message);

        $messageForm->handleRequest($request);

        if ($messageForm->isSubmitted() && $messageForm->isValid()) {
            return $messageService->handleMessageFormData($messageForm, $discussion);
        }

        return new JsonResponse([
            'html' => $environment->render('message/message.html.twig', [
                'formMessage' => $messageForm->createView(),
            ]),
            'messages' => $environment->render('message/list.html.twig', [
                'discussion' => $discussion,
                'page' => $page,
                'numbrePagesPagination' => $messagesPaginationInfos['numbrePagesPagination'],
                'messages' => $messagesPaginationInfos['data'],
            ]),
        ]);
    }

    private function setDiscussioReadingMessageStatus(
        EntityManagerInterface $em, 
        Discussion $discussion, 
        User $user) : void
    {
        if ($user == $discussion->getPersonOne()) {
            $discussion->setPersonTwoNumberUnreadMessages(null);
        } else {
            $discussion->setPersonOneNumberUnreadMessages(null);
        }

        $discussion->setModifierUser($user);

        $em->persist($discussion);

        $em->flush();
    }
}
