<?php

namespace App\Controller\Messaging;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Type\Message\MessageFormType;
use App\Controller\Messaging\MessageService;
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
    #[Route('/create/message', name: 'app_create_message')]
    public function create(
        Request $request,
        RequestStack $requestStack, 
        MessageService $messageService, 
        Environment $environment,
        EntityManagerInterface $em,
        Security $security) : JsonResponse
    {    
        $message = new Message();
        $idDiscussion = $request->get('id'); 
        $discussion = $em->getRepository(Discussion::class)->find($idDiscussion);  
        $user = $security->getUser();
        $request = $requestStack->getMainRequest();

        $messageForm = $this->createForm(MessageFormType::class, $message);

        $messageForm->handleRequest($request);

        $messages = $messageService->getMessages($user, $discussion);

        if ($messageForm->isSubmitted() && $messageForm->isValid()) {
            return $messageService->handleMessageFormData($messageForm, $discussion);
        }

        return new JsonResponse([
            'html' => $environment->render('message/message.html.twig', [
                'formMessage' => $messageForm->createView(),
            ]),
            'messages' => $environment->render('message/list.html.twig', [
                'messages' => $messages
            ]),
        ]);
    }
}
