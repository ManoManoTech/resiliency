<?php

namespace Resiliency\Clients;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Resiliency\Exceptions\UnavailableService;
use Resiliency\Contracts\Service;
use Resiliency\Contracts\Place;

/**
 * Symfony implementation of client.
 * The possibility of extending this client is intended.
 * /!\ The HttpClient of Symfony is experimental.
 */
class SymfonyClient extends ClientHelper
{
    /**
     * @var HttpClientInterface the Symfony HTTP client
     */
    private $httpClient;

    public function __construct(
        HttpClientInterface $httpClient,
        array $mainOptions = []
    ) {
        $this->httpClient = $httpClient;
        parent::__construct($mainOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function request(Service $service, Place $place): string
    {
        $options = [];
        try {
            $method = $this->defineMethod($service->getParameters());
            $options['timeout'] = $place->getTimeout();

            $clientParameters = array_merge($service->getParameters(), $options);
            unset($clientParameters['method']);

            return $this->httpClient->request($method, $service->getURI(), $clientParameters)->getContent();
        } catch (TransportExceptionInterface $exception) {
            throw new UnavailableService(
                $exception->getMessage(),
                (int) $exception->getCode(),
                $exception
            );
        }
    }
}
