<?php

declare(strict_types=1);

include_once realpath('.' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'bootstrap.php');

use PHPUnit\Framework\TestCase;
use Framework\Utils\Ary;

class AryTest extends TestCase {

    public function testIsAssociativeArrayPass() {
        $associativeArrayTestPassed = Ary::isAssociative(['hasError' => false, 'message' => 'I am happy!']);
        $this->assertTrue($associativeArrayTestPassed);
    }

    public function testIsAssociativeArrayFail() {
        $associativeArrayTestPassed = Ary::isAssociative([1, 2, "abcd"]);
        $this->assertFalse($associativeArrayTestPassed);
    }

    public function testConvertFromObjectPassForNull() {
        $result = Ary::convertFromObject(null);
        $this->assertEquals([], $result);
    }

    public function testConvertFromObjectPassForSimpleObject() {
        $obj = new stdClass();
        $obj->id = 1;
        $obj->name = "Simple class";
        
        $result = Ary::convertFromObject($obj);

        $expected = ['id' => 1, 'name' => 'Simple class'];

        $this->assertEquals($expected, $result);
        $this->assertArrayHasKey('id', $result);

        unset($obj);
    }

    public function testConvertFromObjectPassForComplexObject() {
        $obj = new stdClass();
        $obj->id = 1;
        $obj->name = "Complex class";
        
        $subset = new stdClass();
        $subset->transactions = [];
        $transaction1 = new stdClass();
        $transaction1->id = 1;
        $transaction1->amount = 200;
        $transaction2 = new stdClass();
        $transaction2->id = 2;
        $transaction2->amount = -500;
        $transaction3 = new stdClass();
        $transaction3->id = 3;
        $transaction3->amount = 0;
        
        array_push($subset->transactions, $transaction1);
        array_push($subset->transactions, $transaction2);
        array_push($subset->transactions, $transaction3);

        $obj->subset = $subset;
        
        $result = Ary::convertFromObject($obj);

        $expected = [
            'id' => 1, 
            'name' => 'Complex class', 
            'subset' => [
                'transactions' => [
                    [
                        'id' => 1,
                        'amount' => 200
                    ],
                    [
                        'id' => 2,
                        'amount' => -500
                    ],
                    [
                        'id' => 3,
                        'amount' => 0
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, $result);
        $this->assertNotEquals($expected, (array) $obj);
        $this->assertArrayHasKey('id', $result);

        unset($transaction3, $transaction2, $transaction1, $subset, $obj);
    }

    public function testConvertFromObjectFailForObject() {
        $obj = new stdClass();
        $obj->age = 11;
        $obj->description = "Simple description";
        $result = Ary::convertFromObject($obj);

        $expected = ['id' => 1, 'name' => 'Simple class'];

        $this->assertNotEquals($expected, $result);
    }

}