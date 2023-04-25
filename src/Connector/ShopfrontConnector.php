<?php

namespace ShopfrontSDK\Connector;

use ShopfrontSDK\Model\Customer;
use ShopfrontSDK\Model\Level;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use ShopfrontSDK\Model\Event;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ShopfrontConnector
{
    const SALES_URL = '/rest/all/V2/shopfront/sales';
    const RETURNS_URL = '/rest/all/V2/shopfront/returns';
    const CUSTOMERS_URL = '/rest/all/V2/shopfront/customers';
    const EVENTS_URL = '/rest/all/V2/shopfront/events';
    const CUSTOMERS_SEARCH_URL = '/rest/all/V1/customers/search';

    private ClientInterface $client;
    private string $host;
    private string $apiKey;

    public function __construct(
        string $host,
        string $apiKey
    ) {
        $this->host = $host;
        $this->apiKey = $apiKey;
        $this->client = new \GuzzleHttp\Client();
    }

    public function getSales(array $options): array
    {
        $uri = self::SALES_URL;
        if (sizeof($options)) {
            $uri .= '?' . http_build_query($options);
        }
        $request = $this->buildRequest($uri);
        return $this->execute($request);
    }

    public function getReturns(array $options): array
    {
        $uri = self::RETURNS_URL;
        if (sizeof($options)) {
            $uri .= '?' . http_build_query($options);
        }
        $request = $this->buildRequest($uri);
        return $this->execute($request);
    }

    public function createCustomer(Customer $customer)
    {
        $uri = self::CUSTOMERS_URL;
        $request = $this->buildRequest($uri, 'POST', ['customer' => $customer]);
        return $this->execute($request);
    }

    public function updateCustomer(Customer $customer)
    {
        $uri = self::CUSTOMERS_URL . "/$customer->enrolleeId";
        $request = $this->buildRequest($uri, 'PUT', ['customer' => $customer]);
        return $this->execute($request);
    }

    public function setLevel(string $enrolleeId, Level $level)
    {
        $uri = self::CUSTOMERS_URL . "/$enrolleeId/levels";
        $request = $this->buildRequest($uri, 'POST', $level);
        return $this->execute($request);
    }

    public function clearLevels()
    {
        $uri = self::CUSTOMERS_URL . "/clear-levels";
        $request = $this->buildRequest($uri, 'POST');
        return $this->execute($request);
    }

    public function createEvent(Event $event)
    {
        $uri = self::EVENTS_URL;
        $request = $this->buildRequest($uri, 'POST', ['event' => $event]);
        return $this->execute($request);
    }

    public function updateEvent(string $id, Event $event)
    {
        $uri = self::EVENTS_URL . "/$id";
        $request = $this->buildRequest($uri, 'PUT', ['event' => $event]);
        return $this->execute($request);
    }

    public function patchEmail(Customer $customer, string $newEmail)
    {
        $uri = self::CUSTOMERS_URL . "/$customer->enrolleeId";
        $body = [
            'email' => $newEmail
        ];
        $request = $this->buildRequest($uri, 'PUT', ['customer' => $body]);
        return $this->execute($request);
    }

    public function searchCustomers(array $searchCriteria)
    {
        $uri = self::CUSTOMERS_SEARCH_URL;
        $queryParams = $this->buildSearchCriteria($searchCriteria);
        if ($queryParams) {
            $uri .= '?' . http_build_query($queryParams);
        }

        $request = $this->buildRequest($uri);
        return $this->execute($request);
    }

    protected function buildSearchCriteria(array $params)
    {
        $query = [];

        if (!sizeof($params)) {
            return '';
        }

        if (isset($params['currentPage'])) {
            $query['searchCriteria[currentPage]'] = $params['currentPage'];
        }
        if (isset($params['pageSize'])) {
            $query['searchCriteria[pageSize]'] = $params['pageSize'];
        }
        if (isset($params['sortOrders'])) {
            for ($i = 0; $i < sizeof($params['sortOrders']); $i++) {
                if (isset($params['sortOrders'][$i]['direction']) && isset($params['sortOrders'][$i]['field'])) {
                    $query["searchCriteria[sortOrders][$i][direction]"] = $params['sortOrders'][$i]['direction'];
                    $query["searchCriteria[sortOrders][$i][field]"] = $params['sortOrders'][$i]['field'];
                }
            }
        }
        if (isset($params['filterGroups'])) {
            for ($i = 0; $i < sizeof($params['filterGroups']); $i++) {
                for ($j = 0; $j < sizeof($params['filterGroups'][$i]['filters']); $j++) {
                    if (
                        isset($params['filterGroups'][$i]['filters'][$j]['field'])
                        && isset($params['filterGroups'][$i]['filters'][$j]['value'])
                    ) {
                        $query["searchCriteria[filterGroups][$i][filters][$j][field]"] =
                            $params['filterGroups'][$i]['filters'][$j]['field'];
                        $query["searchCriteria[filterGroups][$i][filters][$j][value]"] =
                            $params['filterGroups'][$i]['filters'][$j]['value'];

                        if (isset($params['filterGroups'][$i]['filters'][$j]['conditionType'])) {
                            $query["searchCriteria[filterGroups][$i][filters][$j][conditionType]"] =
                                $params['filterGroups'][$i]['filters'][$j]['conditionType'];
                        }
                    }
                }
            }
        }

        return $query;
    }

    protected function buildRequest(string $path, string $method = 'GET', $body = null): RequestInterface
    {
        if ($body) {
            $body = json_encode($body);
        }
        $request = (new Request(
            $method,
            $this->host . $path,
            [],
            $body
        ))
            ->withAddedHeader('Authorization', 'Bearer ' . $this->apiKey)
            ->withAddedHeader('Content-Type', 'application/json');
        return $request;
    }

    protected function execute(RequestInterface $req)
    {
        $resp = $this->client->sendRequest($req);
        $body = (string) $resp->getBody();

        if ($resp->getStatusCode() >= 300) {
            throw new HttpException($resp->getStatusCode(), $body);
        }
        if (!$body) {
            return null;
        }
        return json_decode($body, true, JSON_THROW_ON_ERROR);
    }
}
