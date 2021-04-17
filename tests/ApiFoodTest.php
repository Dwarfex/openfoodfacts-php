<?php

use GuzzleHttp\Exception\ServerException;
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

class ApiFoodTest extends TestCase
{

    use FilesystemTrait;

    private $api;

    protected function setUp()
    {
        $log = new Logger('name');
        $log->pushHandler(new StreamHandler('log/test.log'));

        $this->api = new Api('food', 'fr-en', $log);
        @rmdir('tests/tmp');
        @mkdir('tests/tmp');
    }

    public function testApi(): void
    {
        $prd = $this->api->getProduct('3057640385148');

        $this->assertInstanceOf(FoodDocument::class, $prd);
        $this->assertInstanceOf(Document::class, $prd);
        $this->assertTrue(isset($prd->product_name));
        $this->assertNotEmpty($prd->product_name);

        try {
            $product = $this->api->getProduct('305764038514800');
            $this->assertInstanceOf(Document::class, $product);
            $this->assertTrue(false);
        } catch (ProductNotFoundException $e) {
            $this->assertTrue(true);
        }

        try {
            $result = $this->api->downloadData('tests/mongodb', 'nopeFile');
            $this->assertIsBool($result);
            $this->assertTrue(false);
        } catch (BadRequestException $e) {
            $this->assertEquals('File type not recognized!', $e->getMessage());
        }

        // $result = $this->api->downloadData('tests/tmp/mongodb');
        // $this->assertTrue(true);
    }

    public function testApiCollection(): void
    {
        $page = 3;
        $collection = $this->api->getByFacets([]);
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals(0, $collection->pageCount());

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

    public function testApiAddProduct(): void
    {
        $this->api->activeTestMode();
        $postData = [];
        try {
            $prd = $this->api->getProduct('3057640385148');
            $this->assertInstanceOf(FoodDocument::class, $prd);
            $this->assertInstanceOf(Document::class, $prd);
            $postData = ['code' => $prd->code, 'product_name' => $prd->product_name];
        } catch (Exception $exception) {
            if ($exception->getPrevious() instanceof ServerException && $exception->getPrevious()->getCode() === 503) {
                $this->markTestSkipped(
                    'Testing API currently not available.'
                );
            }
        }


        $result = $this->api->addNewProduct($postData);
        $this->assertTrue(is_bool($result));


        $postData = ['product_name' => $prd->product_name];

        try {
            $result = $this->api->addNewProduct($postData);
            $this->assertNotNull($result);
            $this->assertTrue(false);
        } catch (BadRequestException $e) {
            $this->assertTrue(true);
        }
        $postData = ['code' => '', 'product_name' => $prd->product_name];
        $result   = $this->api->addNewProduct($postData);
        $this->assertTrue(is_string($result));
        $this->assertEquals('no code or invalid code', $result);
    }

    public function testApiAddImage(): void
    {
        $this->api->activeTestMode();
        try {
            $prd = $this->api->getProduct('3057640385148');
            $this->assertInstanceOf(Collection::class, $prd);
        } catch (Exception $exception) {
            if ($exception->getPrevious() instanceof ServerException && $exception->getPrevious()->getCode() === 503) {
                $this->markTestSkipped(
                    'Testing API currently not available.'
                );
            }
        }

        try {
            $this->api->uploadImage('3057640385148', 'fronts', 'nothing');
            $this->assertTrue(false);
        } catch (BadRequestException $e) {
            $this->assertEquals('ImageField not valid!', $e->getMessage());
        }
        try {
            $this->api->uploadImage('3057640385148', 'front', 'nothing');
            $this->assertTrue(false);
        } catch (BadRequestException $e) {
            $this->assertEquals('Image not found', $e->getMessage());
        }
        $file1 = $this->createRandomImage();

        $result = $this->api->uploadImage('3057640385148', 'front', $file1);
        $this->assertEquals('status ok', $result['status']);
        $this->assertTrue(isset($result['imagefield']));
        $this->assertTrue(isset($result['image']));
        $this->assertTrue(isset($result['image']['imgid']));
    }

    public function testApiSearch(): void
    {
        $collection = $this->api->search('volvic', 3, 30);
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals(30, $collection->pageCount());
        $this->assertGreaterThan(100, $collection->searchCount());
    }

    public function testAvailableFacets(): void
    {
        $testcases =
            [
                'additives' =>'en:e160c',
                'allergens' => 'en:eggs',
                'brands' =>'Fleury Michon',
                'categories' =>'en:fishes',
                'countries' => 'en:france', // France
                'contributors' =>'hungergames',
                'code' => '8017596101280',
                'entry_dates' => '2018',
                'ingredients' => 'en:water',
                'label' =>'Organic',
                'languages' => 'en:italian',
                'nutrition_grade' =>'b',
                'packaging' =>'en:plastic',
                'packaging_codes',
                'purchase_places' =>'France',
                'photographer' =>'openfoodfacts-contributors',
                'informer' => 'openfoodfacts-contributors',
                'states' =>'en:to-be-completed',
                'stores' =>'Magasins U',
                'traces' =>'en:eggs',
            ];
        foreach ($testcases as $facet => $value){
            if(empty($value)){
                continue;
            }
            $result = $this->api->getByFacets([$facet => $value]);
            $e =1;
        }
    }


    public function testFacets(): void
    {
        $collection = $this->api->getIngredients();
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertIsInt($collection->pageCount());
        $this->assertIsInt($collection->getPageSize());
        $this->assertGreaterThan(70000, $collection->searchCount());

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


    private function createRandomImage(): string
    {
        $width  = 400;
        $height = 200;

        $imageRes = imagecreatetruecolor($width, $height);
        for ($row = 0; $row <= $height; $row++) {
            for ($column = 0; $column <= $width; $column++) {
                $colour = imagecolorallocate($imageRes, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
                imagesetpixel($imageRes, $column, $row, $colour);
            }
        }
        $path = 'tests/tmp/image_' . time() . '.jpg';
        if (imagejpeg($imageRes, $path)) {
            return $path;
        }
        throw new Exception("Error Processing Request", 1);
    }

    protected function tearDown()
    {
        $this->recursiveDeleteDirectory('tests/tmp');
    }

}
