<?php

namespace App\Controller\Ticket;

use App\Entity\Ticket;
use App\Entity\TicketEquipment;
use App\Form\Type\Ticket\TicketFormType;
use App\Form\Type\Ticket\TicketEquipmentFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\Breadcrumb\BreadcrumbService;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Search\Ticket\TicketSearch;
use App\UseCase\Ticket\CreateTicketUseCase;
use App\UseCase\Ticket\UpdateTicketUseCase;
use App\UseCase\Ticket\DeleteTicketUseCase;
use App\UseCase\Ticket\GetTicketUseCase;
use App\UseCase\Ticket\SearchTicketUseCase;
use App\UseCase\Ticket\GetTicketMessagesUseCase;
use App\UseCase\Ticket\GetTicketEquipmentsUseCase;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Enum\TicketStatus;
use App\Enum\TicketEquipmentStatus;
use App\Enum\TicketType;
use App\Enum\TicketPriority;
use App\UseCase\Exception\ValidationException;
use App\Form\Type\Ticket\TicketSoftwareFormType;
use App\Entity\TicketSoftware;
use App\Enum\TicketSoftwareStatus;
use App\UseCase\Ticket\DeleteTicketEquipmentRowUseCase;
use App\UseCase\Ticket\DeleteTicketSoftwareRowUseCase;
use App\UseCase\Ticket\GetTicketSoftwareUseCase;

class TicketController extends AbstractController
{
    public function __construct(
        private TranslatorInterface $translator
    ) {}

    #[Route('/user/tickets', name: 'ticket_list', options: ['expose' => true])]
    public function list(BreadcrumbService $breadcrumbService): Response
    {
        $breadcrumbService->addBreadcrumb('list_ticket', $this->generateUrl('ticket_list'));

        $form = $this->createForm(TicketFormType::class, new Ticket('', ''));

        $statusOptions = array_map(function (TicketStatus $status) {
            return [
                'value' => $status->value,
                'label' => $this->translator->trans('ticket.enum.' . strtolower($status->name)),
            ];
        }, TicketStatus::cases());

        return $this->render('ticket/list.html.twig', [
            'form' => $form->createView(),
            'statusOptions' => $statusOptions,
        ]);
    }

    // =======================
    // CREATE
    // =======================
    #[Route('/user/tickets/create', name: 'ticket_create', methods: ['POST'], options: ['expose' => true])]
    public function create(Request $request, CreateTicketUseCase $useCase): JsonResponse
    {
        $data = $this->getJsonData($request);

        if (!$data) {
            return $this->error('Invalid request data', 400);
        }

        try {
            $ticket = $useCase->execute($data);

            return $this->json([
                'success' => true,
                'message' => $this->translation('ticket_created'),
                'id' => $ticket->getId(),
            ], 201);

        } catch (ValidationException $e) {
            return $this->json([
                'errors' => $e->getErrors()
            ], 422);
        }
    }

    // =======================
    // UPDATE
    // =======================
    #[Route('/user/tickets/{id}/update', name: 'ticket_update', methods: ['POST'], options: ['expose' => true])]
    public function update(Request $request, Ticket $ticket, UpdateTicketUseCase $useCase): JsonResponse
    {
        $data = $this->getJsonData($request);

        if (!$data) {
            return $this->error('Invalid request data', 400);
        }

        try {
            $useCase->execute($ticket, $data);
        
            return $this->success($this->translation('ticket_modified'));
        
        } catch (ValidationException $e) {
            return $this->json([
                'errors' => $e->getErrors()
            ], 422);
        }
    }

    #[Route(
        '/user/tickets/messages/{id}',
        name: 'get_ticket_messages',
        methods: ['GET'],
        requirements: ['id' => '\d+'],
        options: ['expose' => true]
    )]
    public function ticketMessages(int $id, GetTicketMessagesUseCase $useCase): Response
    {
        $ticketMessages = $useCase->execute($id);

        $html = $this->renderView('ticket/message.html.twig', [
            'messages' => $ticketMessages
        ]);

        return new Response($html);
    }

    #[Route(
        '/user/form/tickets/equipments/{id}',
        name: 'get_form_ticket_equipments',
        methods: ['GET'],
        requirements: ['id' => '\d+'],
        options: ['expose' => true]
    )]
    public function ticketEquipments(int $id, GetTicketEquipmentsUseCase $useCase): Response
    {
        $statusOptions = array_map(function (TicketEquipmentStatus $status) {
            return [
                'value' => $status->value,
                'label' => $this->translator->trans(strtolower($status->name)),
            ];
        }, TicketEquipmentStatus::cases());

        $ticketEquipments = $useCase->execute($id);

        $html = $this->renderView('ticket/equipment.html.twig', [
            'ticketEquipments' => $ticketEquipments,
            'statusOptions' => $statusOptions
        ]);

        return new Response($html);
    }

    #[Route('/ticket/{id}/software', name: 'get_form_ticket_software', methods: ['GET'], options: ['expose' => true])]
    public function getTicketSoftware(int $id, GetTicketSoftwareUseCase $useCase): Response
    {
        $statusOptions = array_map(
            fn (TicketSoftwareStatus $status) => [
                'value' => $status->value,
                'label' => $this->translator->trans(strtolower($status->name)),
            ],
            TicketSoftwareStatus::cases()
        );

        $ticketSoftwares = $useCase->execute($id);

        return $this->render('ticket/_ticket_software.html.twig', [
            'ticketSoftwares' => $ticketSoftwares,
            'statusOptions' => $statusOptions
        ]);
    }

    #[Route(
        '/user/form/tickets/row/equipments',
        name: 'get_form_row_ticket_equipments',
        methods: ['GET'],
        options: ['expose' => true]
    )]
    public function ticketRowEquipments(): Response
    {
        $ticketEquipment = new TicketEquipment();

        $form = $this->createForm(TicketEquipmentFormType::class, $ticketEquipment);

        $formView = $form->createView();

        $html = $this->renderView('ticket/row_quipment.html.twig', [
            'form' => $form->createView()
        ]);

        return new Response($html);
    }

    #[Route(
        '/user/form/tickets/row/software',
        name: 'get_form_row_ticket_software',
        methods: ['GET'],
        options: ['expose' => true]
    )]
    public function ticketRowSoftware(): Response
    {
        $ticketSoftware = new TicketSoftware();
    
        $form = $this->createForm(TicketSoftwareFormType::class, $ticketSoftware);
    
        return new Response(
            $this->renderView('ticket/row_software.html.twig', [
                'form' => $form->createView()
            ])
        );
    }

    #[Route(
        '/user/tickets/{id}',
        name: 'get_ticket',
        methods: ['GET'],
        requirements: ['id' => '\d+'],
        options: ['expose' => true]
    )]
    public function show(int $id, GetTicketUseCase $getTicket): JsonResponse
    {
        $ticket = $getTicket->execute($id);
    
        return $this->json($ticket);
    }

    // =======================
    // DELETE
    // =======================
    #[Route('/user/tickets/{id}/delete', name: 'ticket_delete', methods: ['DELETE'], options: ['expose' => true])]
    public function delete(Ticket $ticket, DeleteTicketUseCase $useCase): JsonResponse
    {
        try {
            $useCase->execute($ticket);

            return $this->success($this->translation('ticket_removed'));

        } catch (\RuntimeException $e) {
            return $this->error('Unable to delete ticket', 500);
        }
    }

    // =======================
    // SEARCH
    // =======================
    #[Route('/user/tickets/search', name: 'ticket_search_ajax', methods: ['GET'], options: ['expose' => true])]
    public function search(Request $request, SearchTicketUseCase $useCase): Response
    {
        $result = $useCase->execute(
            $request->get('title'),
            $request->get('description'),
            $request->get('status'),
            (int) $request->get('page', 1)
        );

        return $this->render('ticket/_table_rows.html.twig', [
            'tickets' => $result['items'],
            'pagination' => $result['pagination']
        ]);
    }

    #[Route('/user/tickets/{id}/delete/equipment/row', name: 'ticket_equipment_delete_row', methods: ['DELETE'], options: ['expose' => true])]
    public function deleteEquipmentRow(int $id, DeleteTicketEquipmentRowUseCase $useCase): JsonResponse
    {
        $result = $useCase->execute($id);

        $message = $result['message'];

        // =========================
        // MESSAGE STOCK UPDATE
        // =========================
        if (!empty($result['stockUpdated'])) {
            $message .= $this->translation('stock_updated_available');
        }

        return $this->json([
            'success' => $result['success'],
            'message' => $message
        ], $result['status']);
    }

    #[Route('/user/tickets/{id}/delete/software/row', name: 'ticket_software_delete_row', methods: ['DELETE'], options: ['expose' => true])]
    public function deleteSoftwareRow(
        int $id,
        DeleteTicketSoftwareRowUseCase $useCase
    ): JsonResponse {
        $result = $useCase->execute($id);

        $message = $result['message'];

        if (!empty($result['licenseRevoked'])) {
            $message .= $this->translation('license_revoked');
        }

        return $this->json([
            'success' => $result['success'],
            'licenseRevoked' => $result['licenseRevoked'],
            'message' => $message,
        ], $result['status']);
    }

    // =======================
    // HELPERS
    // =======================

    private function getJsonData(Request $request): ?array
    {
        return json_decode($request->getContent(), true);
    }

    private function success(string $message): JsonResponse
    {
        return $this->json([
            'success' => true,
            'message' => $message
        ]);
    }

    private function error(string $message, int $status = 400): JsonResponse
    {
        return $this->json([
            'success' => false,
            'errors' => [
                'general' => $message
            ]
        ], $status);
    }

    private function translation(string $label): string
    {
        return $this->translator->trans($label);
    }
}
