<?php

namespace App\Controller\Stock;

use App\Entity\EquipmentAsset;
use App\Form\Type\Stock\EquipmentFormType;
use App\Repository\EquipmentAssetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\Breadcrumb\BreadcrumbService;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/manager/stock')]
class EquipmentController extends AbstractController
{
    // =========================
    // LIST
    // =========================
    #[Route('/', name: 'admin_stock', options: ['expose' => true])]
    public function index(
        EquipmentAssetRepository $repo,
        Request $request,
        BreadcrumbService $breadcrumbService,
        PaginatorInterface $paginator): Response
    {
        $breadcrumbService->addBreadcrumb('list_stock', $this->generateUrl('admin_stock'));

        $search = $request->query->get('search');

        $qb = $repo->createQueryBuilder('e')
            ->orderBy('e.id', 'DESC');

        if ($search) {
            $qb->andWhere(
                $qb->expr()->orX(
                    'e.serialNumber LIKE :search',
                    'e.reference LIKE :search'
                )
            )
            ->setParameter('search', '%' . $search . '%');
        }

        $equipments = $paginator->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('stock/index.html.twig', [
            'equipments' => $equipments,
        ]);
    }

    // =========================
    // FORM (CREATE / EDIT)
    // =========================
    #[Route('/form/{id}', name: 'admin_stock_form', defaults: ['id' => null], options: ['expose' => true])]
    public function form(
        ?EquipmentAsset $equipment,
        Request $request,
        EntityManagerInterface $em
    ): Response {

        $isNew = !$equipment;
        $equipment ??= new EquipmentAsset();

        $form = $this->createForm(EquipmentFormType::class, $equipment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // =========================
            // METIER CENTRALISE
            // =========================
            $equipment->setUpdatedBy($this->getUser());

            if ($isNew) {
                $equipment->setCreatedBy($this->getUser());
                $em->persist($equipment);
            }

            // =========================
            // RULES METIER
            // =========================
            $equipment->assignTo($equipment->getAssignedTo());

            $em->flush();

            return $this->json([
                'success' => true,
                'message' => $isNew ? 'Created' : 'Updated'
            ]);
        }

        return $this->render('stock/_form.html.twig', [
            'form' => $form->createView(),
            'equipment' => $equipment
        ]);
    }

    // =========================
    // DELETE
    // =========================
    #[Route('/delete/{id}', name: 'admin_stock_delete', methods: ['POST'], options: ['expose' => true])]
    public function delete(
        EquipmentAsset $equipment,
        EntityManagerInterface $em,
        Request $request
    ): JsonResponse {

        if (!$this->isCsrfTokenValid('delete'.$equipment->getId(), $request->request->get('_token'))) {
            return $this->json(['success' => false], 403);
        }

        try {
            $em->remove($equipment);
            $em->flush();

            return $this->json([
                'success' => true,
                'message' => 'Deleted'
            ]);

        } catch (\Exception $e) {

            return $this->json([
                'success' => false,
                'error' => 'Delete failed'
            ], 500);
        }
    }
}