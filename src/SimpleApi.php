<?php


namespace OpenFoodFacts;


use GuzzleHttp\Client;
use Psr\Log\NullLogger;
use Psr\SimpleCache\InvalidArgumentException;

class SimpleApi
{
    private const WORLDWIDE = 'world';

    public function getProduct(string $barcodeString): ?Document
    {

        $apis = self::getAvailableApis();
        foreach ($apis as $api){
            try {
                $documentsFound[]= $api->getProduct($barcodeString);
            }catch (Exception\BadRequestException $e) {
                //TODO
            }catch (Exception\ProductNotFoundException $e) {
                continue;
            }catch (InvalidArgumentException $e) {
                //TODO
            }
        }
    }

    public function find(string $searchString): ?Collection
    {
        $apis = self::getAvailableApis();
        $foundCollections = [];
        foreach ($apis as $api){
            $foundCollections[] = $api->searchAndGetAllPages($searchString);
            $e =1;

        }
        $e =1;
    }

    /**
     * Returns an array of available APIS
     * @return Api[]
     */
    protected static function getAvailableApis(): array
    {
        $logger     = new NullLogger();
        $httpClient = new Client(
            [
                'headers' => [
                    'User-Agent' => 'Open Food Facts - Simple API SDK',
                ],
            ]
        );

        $apis = [];
        foreach (API::LIST_API as $type => $urlPattern) {
            $api         = new Api($type, self::WORLDWIDE, $logger, $httpClient);
            $apis[$type] = $api;
        }

        return $apis;
    }

}