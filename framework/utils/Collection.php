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
 * @inheritdoc
 */
final class Collection implements ICollection {

    /**
     * @var T[]
     */
    private array $_items = [];

    public function __construct(array $items = []) {
        if ($items) {
            foreach($items as $item) {
                $this->_validateItemType($item);

                $this->add($item);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function count(): int {
        if (!$this->_items)
            return 0;

        return count($this->_items);
    }

    /**
     * @inheritdoc
     */
    public function isEmpty(): bool {
        return $this->count() == 0;
    }

    /**
     * @inheritdoc
     */
    public function all() : array {
        return $this->_items;
    }

    /**
     * @inheritdoc
     */
    public function first() {
        if ($this->count() == 0)
            return null;

        return $this->_items[0];
    }

    /**
     * @inheritdoc
     */
    public function last() {
        $numberOfItems = $this->count();
        if ($numberOfItems == 0)
            return null;

        return $this->_items[$numberOfItems - 1];
    }

    /**
     * @inheritdoc
     */
    public function add($item) : ICollection {
        $this->_validateItemType($item);
        
        $this->_items[] = $item;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function remove($item) : void {
        $index = $this->getIndexOf($item);
        if ($index == -1)
            throw new ItemNotFoundException('Item not found in the collection.');

        unset($this->_items[$index]);
    }

    /**
     * @inheritdoc
     */
    public function removeAt(int $index): void {
        if ($this->getElementAt($index))
            unset($this->_items[$index]);
    }

    /**
     * @inheritdoc
     */
    public function merge(ICollection $collection) : array {
        if (!$collection)
            throw new InvalidArgumentException('Collection to merge cannot be empty');

        if ($collection->count() == 0)
            return $this->all();

        $itemsToMerge = $collection->all();
        foreach($itemsToMerge as $item) {
            $this->add($item);
        }

        return $this->all();
    }

    /**
     * @inheritdoc
     */
    public function contains($item) : bool {
        if ($this->count() == 0)
            return false;

        $this->_validateItemType($item);
        
        return in_array($item, $this->_items);
    }

    /**
     * @inheritdoc
     */
    public function getElementAt(int $index) : mixed {
        if ($this->count() == 0)
            throw new NoItemFoundException('There are no items found in the collection.');

        if (!isset($this->_items[$index]))
            throw new ItemNotFoundException("There was no item found at index {$index}.");

        return $this->_items[$index];
    }

    /**
     * @inheritdoc
     */
    public function getIndexOf($item) : int {
        $this->_validateItemType($item);

        $count = $this->count();
        if ($count == 0)
            return -1;

        return array_search($item, $this->_items);
    }

    /**
     * @inheritdoc
     */
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

    /**
     * Verifies that item being compared is the same as the underlying items type.
     * Throws InvalidArgumentException if the types don't match or throws InvalidOperationException if it doesn't implement the same interface.
     * 
     * @param T $item
     */
    private function _validateItemType($item) {
        if ($this->isEmpty())
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

    /**
     * Returns the underlying type for item
     * 
     * @param T
     */
    private function _getItemType($item) : string {
        return gettype($item);
    }

    /**
     * Returns a value, indicating wether the underlying items in an associative array or not.
     */
    private function _isAssociative() : bool {
        if (array() === $this->_items) return false;
        return array_keys($this->_items) !== range(0, count($this->_items) - 1);        
    }

    public function getIterator(): Traversable {
        return new ArrayIterator($this->_items);
    }
}