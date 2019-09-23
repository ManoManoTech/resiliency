<?php

namespace Resiliency\Clients;

use GuzzleHttp\Client as OriginalGuzzleClient;
use Resiliency\Exceptions\UnavailableService;
use Psr\Http\Message\ResponseInterface;
use Resiliency\Contracts\Service;
use Resiliency\Contracts\Place;
use Exception;

/**
 * Guzzle implementation of client.
 * The possibility of extending this client is intended.
 */
class GuzzleClient extends ClientHelper
{
    /**
     * {@inheritdoc}
     */
    public function request(Service $service, Place $place): ResponseInterface
    {
        $options = [];
        try {
            $client = new OriginalGuzzleClient($this->mainOptions);
            $method = $this->defineMethod($service->getParameters());
            $options['http_errors'] = true;
            $options['connect_timeout'] = $place->getTimeout();
            $options['timeout'] = $place->getTimeout();

            $clientParameters = array_merge($service->getParameters(), $options);

            return $client->request($method, $service->getURI(), $clientParameters);
        } catch (Exception $exception) {
            throw new UnavailableService(
                $exception->getMessage(),
                (int) $exception->getCode(),
                $exception
            );
        }
    }
}
