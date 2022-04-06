<?php

namespace ShopfrontSDK\Connector;

use ShopfrontSDK\Model\Ambassador;
use ShopfrontSDK\Model\Level;
use ShopfrontSDK\Model\LevelClearance;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ShopfrontConnector
{
    const SALES_URL = '/api/plumtree/sales';
    const AMBASSADORS_URL = '/api/plumtree/ambassadors';

    private ClientInterface $client;

    public function __construct(
        private string $drupalHost,
        private string $drupalApiKey,
    )
    {
        $this->client = new \GuzzleHttp\Client();
    }

    public function getSales(string $timestamp = ''): array
    {
        $uri = self::SALES_URL;
        if($timestamp != ''){
            $uri .= '?dateFrom='.$timestamp;
        }
        $request = $this->buildRequest($uri);
        return $this->execute($request);
    }

    public function createAmbassador(Ambassador $ambassador): void
    {
        $uri = self::AMBASSADORS_URL;
        $request = $this->buildRequest($uri, 'POST', $ambassador);
        $this->execute($request);
    }

    public function activateAmbassador(string $ambasadorId): void
    {
        $uri = self::AMBASSADORS_URL."/$ambasadorId/activate";
        $request = $this->buildRequest($uri, 'POST');
        $this->execute($request);
    }

    public function setLevel(string $ambasadorId, Level $level): void
    {
        $uri = self::AMBASSADORS_URL."/$ambasadorId/levels";
        $request = $this->buildRequest($uri, 'POST', $level);
        $this->execute($request);
    }

    public function clearLevels(LevelClearance $lc): void
    {
        $uri = self::AMBASSADORS_URL."/levels/clear";
        $request = $this->buildRequest($uri, 'POST', $lc);
        $this->execute($request);
    }

    public function patchEmail(Ambassador $ambassador, string $newEmail)
    {
        $uri = self::AMBASSADORS_URL;
        $body = [
            'email' => $ambassador->email,
            'newEmail' => $newEmail
        ];
        $request = $this->buildRequest($uri, 'PATCH', $body);
        $this->execute($request);
    }

    protected function buildRequest(string $path, string $method = 'GET', $body = null): RequestInterface
    {
        if ($body){
            $body = json_encode($body);
        }
        $request = (new Request(method: $method, uri: $this->drupalHost.$path, body: $body))
                    ->withAddedHeader('Authorization', 'Bearer '.$this->drupalApiKey);
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
