<?php

use Illuminate\Support\Arr;

class SupportArrTest extends PHPUnit_Framework_TestCase
{
    public function testIsAssoc()
    {
        $this->assertTrue(Arr::isAssoc(['a' => 'a', 0 => 'b']));
        $this->assertTrue(Arr::isAssoc([1 => 'a', 0 => 'b']));
        $this->assertTrue(Arr::isAssoc([1 => 'a', 2 => 'b']));
        $this->assertFalse(Arr::isAssoc([0 => 'a', 1 => 'b']));
        $this->assertFalse(Arr::isAssoc(['a', 'b']));
    }

    public function testSortRecursive()
    {
        $array = [
            'users' => [
                [
                    // should sort associative arrays by keys
                    'name' => 'joe',
                    'mail' => 'joe@example.com',
                    // should sort deeply nested arrays
                    'numbers' => [2, 1, 0],
                ],
                [
                    'name' => 'jane',
                    'age' => 25,
                ],
            ],
            'repositories' => [
                // should use weird `sort()` behavior on arrays of arrays
                ['id' => 1],
                ['id' => 0],
            ],
            // should sort non-associative arrays by value
            20 => [2, 1, 0],
            30 => [
                // should sort non-incrementing numerical keys by keys
                2 => 'a',
                1 => 'b',
                0 => 'c',
            ],
        ];

        $expect = [
            20 => [0, 1, 2],
            30 => [
                0 => 'c',
                1 => 'b',
                2 => 'a',
            ],
            'repositories' => [
                ['id' => 0],
                ['id' => 1],
            ],
            'users' => [
                [
                    'age' => 25,
                    'name' => 'jane',
                ],
                [
                    'mail' => 'joe@example.com',
                    'name' => 'joe',
                    'numbers' => [0, 1, 2],
                ],
            ],
        ];

        $this->assertEquals($expect, Arr::sortRecursive($array));
    }

    public function testForget()
    {
        $array = ['products' => ['desk' => ['price' => 100]]];
        Arr::forget($array, 'products.desk');
        $this->assertEquals(['products' => []], $array);

        $array = ['products' => ['desk' => ['price' => 100]]];
        Arr::forget($array, 'products.desk.price');
        $this->assertEquals(['products' => ['desk' => []]], $array);

        $array = ['products' => ['desk' => ['price' => 100]]];
        Arr::forget($array, 'products.final.price');
        $this->assertEquals(['products' => ['desk' => ['price' => 100]]], $array);

        $array = ['shop' => ['cart' => [150 => 0]]];
        Arr::forget($array, 'shop.final.cart');
        $this->assertEquals(['shop' => ['cart' => [150 => 0]]], $array);

        $array = ['products' => ['desk' => ['price' => ['original' => 50, 'taxes' => 60]]]];
        Arr::forget($array, 'products.desk.price.taxes');
        $this->assertEquals(['products' => ['desk' => ['price' => ['original' => 50]]]], $array);

        $array = ['products' => ['desk' => ['price' => ['original' => 50, 'taxes' => 60]]]];
        Arr::forget($array, 'products.desk.final.taxes');
        $this->assertEquals(['products' => ['desk' => ['price' => ['original' => 50, 'taxes' => 60]]]], $array);
    }
}
