<?php

namespace ShopfrontSDK\Connector;

use ShopfrontSDK\Model\Ambassador;
use ShopfrontSDK\Model\Level;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ShopfrontConnector
{
    const SALES_URL = '/rest/all/V2/shopfront/sales';
    const AMBASSADORS_URL = '/rest/all/V2/shopfront/customers';

    private ClientInterface $client;

    public function __construct(
        private string $host,
        private string $apiKey,
    ) {
        $this->client = new \GuzzleHttp\Client();
    }

    public function getSales(string $timestamp = ''): array
    {
        $uri = self::SALES_URL;
        if ($timestamp != ''){
            $uri .= '?dateFrom=' . $timestamp;
        }
        $request = $this->buildRequest($uri);
        return $this->execute($request);
    }

    public function createAmbassador(Ambassador $ambassador): void
    {
        $uri = self::AMBASSADORS_URL;
        $request = $this->buildRequest($uri, 'POST', ['customer' => $ambassador]);
        $this->execute($request);
    }

    // public function activateAmbassador(string $ambasadorId): void
    // {
    //     $uri = self::AMBASSADORS_URL . "/$ambasadorId/activate";
    //     $request = $this->buildRequest($uri, 'POST');
    //     $this->execute($request);
    // }

    public function setLevel(string $ambasadorId, Level $level): void
    {
        $uri = self::AMBASSADORS_URL . "/$ambasadorId/levels";
        $request = $this->buildRequest($uri, 'POST', $level);
        $this->execute($request);
    }

    public function clearLevels(string $ambassadorId): void
    {
        $uri = self::AMBASSADORS_URL . "/$ambassadorId/levels";
        $request = $this->buildRequest($uri, 'POST', ['level' => Level::LEVEL_1]);
        $this->execute($request);
    }

    // public function patchEmail(Ambassador $ambassador, string $newEmail)
    // {
    //     $uri = self::AMBASSADORS_URL;
    //     $body = [
    //         'email' => $ambassador->email,
    //         'newEmail' => $newEmail
    //     ];
    //     $request = $this->buildRequest($uri, 'PATCH', $body);
    //     $this->execute($request);
    // }

    protected function buildRequest(string $path, string $method = 'GET', $body = null): RequestInterface
    {
        if ($body) {
            $body = json_encode($body);
        }
        $request = (new Request(method: $method, uri: $this->host . $path, body: $body))
                    ->withAddedHeader('Authorization', 'Bearer ' . $this->apiKey);
        return $request;
    }

    protected function execute(RequestInterface $req)
    {
        $resp = $this->client->sendRequest($req);
        $body = (string) $resp->getBody();

        if ($resp->getStatusCode() >= 300){
            throw new HttpException($resp->getStatusCode(), $body);
        }
        if (!$body) {
            return null;
        }
        return json_decode($body, true, JSON_THROW_ON_ERROR);
    }
}
