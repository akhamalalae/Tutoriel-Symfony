<?php
namespace App\Services\Message;

use App\Contracts\Message\MessageRendererInterface;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment;
use App\Entity\Discussion;
use App\Entity\SearchMessage;
use Symfony\Component\Form\FormInterface;
use App\Contracts\Message\MessageSearchInterface;

class MessageRenderer implements MessageRendererInterface
{
    public function __construct(
        private readonly Environment $environment,
        private readonly EntityManagerInterface $em,
        private readonly MessageSearchInterface $searchService
    ) {}

    public function renderForm(FormInterface $form): string
    {
        return $this->environment->render('message/message_form.html.twig', [
                'formMessage' => $form->createView(),
        ]);
    }

    public function renderMessages(int $idDiscussion, int $page, ?SearchMessage $searchMessage): string
    {
        $messages = $this->searchService
            ->messages($idDiscussion, $page, $searchMessage);

        $discussion = $this->em
            ->getRepository(Discussion::class)->find($idDiscussion); 

        return $this->environment->render('message/list.html.twig', [
            'discussion' => $discussion,
            'messages' => $messages['data'],
            'page' => $page,
            'totalPages' => $messages['totalPages'],
            'searchMessage' => $searchMessage
        ]);
    }
}
