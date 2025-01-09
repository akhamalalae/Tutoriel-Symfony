<?php

namespace App\Controller\Address;

use App\Services\Address\AddressService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AddressController extends AbstractController
{
    #[Route('/user/address/search', name: 'app_search_address', options: ['expose' => true], methods: ['GET'])]
    public function search(Request $request, AddressService $addressService): JsonResponse
    {
        $query = $request->query->get('q', '');

        if (empty($query)) {
            return new JsonResponse([], 400);
        }

        $results = $addressService->searchAddress($query);

        return new JsonResponse($results);
    }
}

