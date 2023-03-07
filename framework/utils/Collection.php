<?php

namespace Framework\Utils;

use ArrayIterator;
use Framework\Exceptions\CollectionIsEmptyException;
use Framework\Exceptions\InvalidOperationException;
use Framework\Exceptions\ItemNotFoundException;
use Framework\Exceptions\KeyNotFoundException;
use Framework\Exceptions\NoItemFoundException;
use Framework\Interfaces\ICollection;
use InvalidArgumentException;
use ReflectionClass;
use Traversable;

/**
 * Represents a strongly typed list of objects that can be accessed by index. Provides various methods to interact with lists.
 */
final class Collection implements ICollection {

    private array $_items = [];

    public function __construct(array $items = []) {
        if ($items) {
            foreach($items as $item) {
                $this->_validateItemType($item);

                $this->add($item);
            }
        }
    }

    public function count(): int {
        if (!$this->_items)
            return 0;

        return count($this->_items);
    }

    public function toList() : array {
        return $this->_items;
    }

    public function first() : mixed {
        if ($this->count() == 0)
            return null;

        return $this->_items[0];
    }

    public function last() : mixed {
        $numberOfItems = $this->count();
        if ($numberOfItems == 0)
            return null;

        return $this->_items[$numberOfItems - 1];
    }

    public function add(mixed $item) : ICollection {
        $this->_validateItemType($item);
        
        $this->_items[] = $item;

        return $this;
    }

    public function remove(mixed $item) : void {
        $index = $this->getIndexOf($item);
        if ($index == -1)
            throw new ItemNotFoundException('Item not found in the collection.');

        unset($this->_items[$index]);
    }

    public function removeAt(int $index): void {
        if ($this->elementAt($index))
            unset($this->_items[$index]);
    }

    public function merge(ICollection $collection) : array {
        if (!$collection)
            throw new InvalidArgumentException('Collection to merge cannot be empty');

        if ($collection->count() == 0)
            return $this->toList();

        $itemsToMerge = $collection->toList();
        foreach($itemsToMerge as $item) {
            $this->add($item);
        }

        return $this->toList();
    }

    public function contains(mixed $item) : bool {
        if ($this->count() == 0)
            return false;

        $this->_validateItemType($item);
        
        return in_array($item, $this->_items);
    }

    public function getElementAt(int $index) : mixed {
        if ($this->count() == 0)
            throw new NoItemFoundException('There are no items found in the collection.');

        if (!isset($this->_items[$index]))
            throw new ItemNotFoundException("There was no item found at index {$index}.");

        return $this->_items[$index];
    }

    public function getIndexOf(mixed $item) : int {
        $this->_validateItemType($item);

        $count = $this->count();
        if ($count == 0)
            return -1;

        return array_search($item, $this->_items);
    }

    public function getIndexByKey(string $key) : int {
        $count = $this->count();
        if ($count == 0)
            return -1;

        if (!$this->_isAssociative())
            throw new InvalidOperationException('Collection is a sequential list and not an object collection.');
        
        if (!array_key_exists($key, $this->_items))
            throw new KeyNotFoundException("{$key} not found in the collection.");

        for($i = 0; $i < $count; $i++) {
            if ($this->_items[$key])
                return $i;
        }

        return -1;
    }

    private function _validateItemType(mixed $item) {
        if ($this->count() == 0)
            return;

        $firstItem = $this->_items[0];

        $firstItemType = $this->_getItemType($firstItem);
        $itemToAddType = $this->_getItemType($item);

        if ($firstItemType !== 'object') {
            if ($itemToAddType !== $firstItemType)
                throw new InvalidArgumentException("{$firstItemType} expected, {$itemToAddType} given.");

            return;
        }

        $firstItemReflectionClass = new ReflectionClass($firstItem);
        $itemToAddReflectionClass = new ReflectionClass($item);

        if (!$firstItemReflectionClass->getInterfaces()) {
            $firstItemReflectionClassName = $firstItemReflectionClass::class;
            $itemToAddReflectionClassName = $itemToAddReflectionClass::class;

            if ($firstItemReflectionClassName !== $itemToAddReflectionClassName)
                throw new InvalidArgumentException("{$firstItemType} expected, {$itemToAddType} given.");

            return;
        }
        
        $interfaceNames = $firstItemReflectionClass->getInterfaceNames();

        if (!$itemToAddReflectionClass->getInterfaces())
            throw new InvalidOperationException(
                $itemToAddReflectionClass::class . ' is expected to implement ' . 
                (count($interfaceNames) == 1 ? $interfaceNames[0] : 'one of ' . join(', ', $interfaceNames)));
    }

    private function _getItemType(mixed $item) : string {
        return gettype($item);
    }

    private function _isAssociative() : bool {
        if (array() === $this->_items) return false;
        return array_keys($this->_items) !== range(0, count($this->_items) - 1);        
    }

    public function getIterator(): Traversable {
        return new ArrayIterator($this->_items);
    }
}