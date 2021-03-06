<?php

use OpenFoodFacts\FilesystemTrait;
use PHPUnit\Framework\Error\Notice;
use PHPUnit\Framework\TestCase;

use OpenFoodFacts\Api;
use OpenFoodFacts\Collection;
use OpenFoodFacts\Document\FoodDocument;
use OpenFoodFacts\Document;
use OpenFoodFacts\Exception\{
    ProductNotFoundException,
    BadRequestException
};


use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

class ApiFoodCacheTest extends TestCase
{
    use FilesystemTrait;

    /**
     * @var Api
     */
    private $api;


    protected function setUp()
    {
        @rmdir('tests/tmp');
        @mkdir('tests/tmp');
        @mkdir('tests/tmp/cache');
        $log = new Logger('name');
        $log->pushHandler(new StreamHandler('log/test.log'));
        $psr6Cache = new FilesystemAdapter(sprintf('testrun_%u', rand(0, 1000)), 10, 'tests/tmp/cache');
        $cache     = new Psr16Cache($psr6Cache);

        $httpClient = new GuzzleHttp\Client([
//            "http_errors" => false, // MUST not use as it crashes error handling
            'Connection' => 'close',
            CURLOPT_FORBID_REUSE => true,
            CURLOPT_FRESH_CONNECT => true,
            'defaults' => [
                'headers' => [
                    'CURLOPT_USERAGENT' => 'OFF - PHP - SDK - Unit Test',
                ],
            ],
        ]);

        $api = new Api('food', 'fr-en', $log, $httpClient, $cache);
        $this->assertInstanceOf(Api::class, $api);
        $this->api = $api;

    }

    public function testApi(): void
    {

        $prd = $this->api->getProduct('3057640385148');

        $this->assertInstanceOf(FoodDocument::class, $prd);
        $this->assertInstanceOf(Document::class, $prd);

        $this->assertTrue(isset($prd->product_name));
        $this->assertNotEmpty($prd->product_name);

        try {
            $this->api->getProduct('305764038514800');
            $this->assertTrue(false);
        } catch (ProductNotFoundException $e) {
            $this->assertTrue(true);
        }

        try {
            $this->api->downloadData('tests/mongodb', 'nopeFile');
            $this->assertTrue(false);
        } catch (BadRequestException $e) {
            $this->assertEquals('File type not recognized!', $e->getMessage());
        }
    }

    public function testApiCollection(): void
    {

        $collection = $this->api->getByFacets([]);
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals(0, $collection->pageCount());
        $page = 3;

        try {
            $collection = $this->api->getByFacets(['trace' => 'egg', 'country' => 'france'], $page);
            $this->assertInstanceOf(Collection::class, $collection);
            $this->assertTrue(false);
        } catch (Notice $e) {
            $this->assertEquals('OpenFoodFact - Your request has been redirect', $e->getMessage());
        }

        $collection = $this->api->getByFacets(['trace' => 'eggs', 'country' => 'france'], $page);
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertGreaterThan(20, $collection->pageCount());
        $this->assertEquals($page, $collection->getPage());
        $this->assertEquals(($page -1 ) *$collection->getPageSize(), $collection->getSkip());
        $this->assertGreaterThan(1000, $collection->searchCount());

        foreach ($collection as $key => $doc) {
            if ($key > 1) {
                break;
            }

            $this->assertInstanceOf(FoodDocument::class, $doc);
            $this->assertInstanceOf(Document::class, $doc);

        }

    }

    public function testApiSearch(): void
    {

        $collection = $this->api->search('volvic', 3, 30);
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals(30, $collection->pageCount());
        $this->assertGreaterThan(100, $collection->searchCount());

    }


    public function testFacets(): void
    {

        $collection = $this->api->getIngredients();
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertGreaterThan(70000, $collection->searchCount());
        $this->assertIsInt($collection->pageCount());
        $this->assertIsInt($collection->getPageSize());

        try {
            $collection = $this->api->getIngredient();
            $this->assertInstanceOf(Collection::class, $collection);
            $this->assertTrue(false);
        } catch (BadRequestException $e) {
            $this->assertEquals('Facet "ingredient" not found', $e->getMessage());
        }

        $collection = $this->api->getPurchase_places();
        $this->assertInstanceOf(Collection::class, $collection);
        $collection = $this->api->getPackaging_codes();
        $this->assertInstanceOf(Collection::class, $collection);
        $collection = $this->api->getEntry_dates();
        $this->assertInstanceOf(Collection::class, $collection);

        try {
            $collection = $this->api->getIngredient();
            $this->assertInstanceOf(Collection::class, $collection);
            $this->assertTrue(false);
        } catch (BadRequestException $e) {
            $this->assertEquals('Facet "ingredient" not found', $e->getMessage());
        }

        try {
            $this->api->nope();
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    protected function tearDown()
    {
        $this->recursiveDeleteDirectory('tests/tmp');
    }

}
