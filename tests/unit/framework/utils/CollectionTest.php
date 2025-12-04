<?php

declare(strict_types=1);

include_once realpath('.' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'bootstrap.php');

use Framework\Exceptions\InvalidOperationException;
use PHPUnit\Framework\TestCase;
use Framework\Utils\Collection;

class CollectionTest extends TestCase {

    public function testCollectionEmptyUponInstantiation() {
        // given
        /** @var Collection<int> */
        $collection = new Collection();

        // when
        $isEmpty = $collection->isEmpty();
        $itemsInCollection = $collection->all();
        $itemsCount = $collection->count();

        // then
        $this->assertTrue($isEmpty);
        $this->assertEmpty($itemsInCollection);
        $this->assertEquals(0, $itemsCount);
    }

    public function testAddItemThrowsInvalidOperationExceptionIfTheItemAddedHasATypeDifferentFromTheActualCollectionTypeAtInstantiation() {
        // given
        /** @var Collection<int> */
        $collection = new Collection();

        $this->expectException(InvalidArgumentException::class);

        // when
        $collection->add(1);
        $collection->add('hello');
    }

}