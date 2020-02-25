<?php

use OpenFoodFacts\FilesystemTrait;
use PHPUnit\Framework\TestCase;

use OpenFoodFacts\Api;
use OpenFoodFacts\Collection;
use OpenFoodFacts\Document\PetDocument;
use OpenFoodFacts\Document;



use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class SimpleApiTest extends TestCase
{

    use FilesystemTrait;

    private $api;

    /**
     *
     */
    protected function setUp(): void
    {
        $this->api = new \OpenFoodFacts\SimpleApi();

    }

    public function testSearch()
    {

        $result = $this->api->find('peanut');
        $e =1;
    }

    public function testBarcode()
    {
        $result = $this->api->getProduct('3270270006737');
        $e =1;
    }



}
