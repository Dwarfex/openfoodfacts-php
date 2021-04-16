<?php

use OpenFoodFacts\Api;
use OpenFoodFacts\Exception\BadRequestException;
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    public function testUploadImageMustThrowAnExceptionForInvalidApi()
    {
        $this->expectExceptionMessage("not Available yet");
        $this->expectException(BadRequestException::class);
        $api = new Api('product');
        $api->uploadImage('unknown', 'foo', 'bar');
    }
}
