<?php

namespace App\Services\Address;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class AddressService
{
    private HttpClientInterface $httpClient;

    private RequestStack $requestStack;

    const URL_API = 'https://nominatim.openstreetmap.org/search';

    public function __construct(HttpClientInterface $httpClient, RequestStack $requestStack)
    {
        $this->httpClient = $httpClient;
        $this->requestStack = $requestStack;
    }

    public function searchAddress(string $query): array
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        $response = $this->httpClient->request('GET', self::URL_API, [
            'query' => [
                'q' => $query,
                'format' => 'json',
                'addressdetails' => 1,
                'limit' => 5,
            ],
            'headers' => [
                'Accept-Language' => $locale,
            ],
        ]);

        return $response->toArray();
    }
}
