<?php

namespace Framework\Interfaces;

use IteratorAggregate;

/**
 * 
 * Represents a collection of objects that can be individually accessed by index.
 * 
 * @template T
 */
interface ICollection extends IteratorAggregate {

    /** 
     * Returns a list that contains elements from the input sequence.
     * 
     * @return T[]
     */
    function all() : array;

    /**
     * Returns a number that represents how many elements in the specified sequence.
     */
    function count() : int;

    /**
     * Determines if the collection is empty or not.
     */
    function isEmpty(): bool;

    /**
     * Returns the first element of a sequence or null if the list is empty.
     * 
     * @return T|null
     */
    function first();

    /**
     * Returns the last element of a sequence or null if the list is empty.
     * 
     * @return T|null
     */
    function last();

    /**
     * Adds an item to a sequence.
     * 
     * @param T $item The object to add to the sequence.
     * 
     * @throws InvalidOperationException if the item can not be added due to it having a type different from the existing type.
     * 
     * @return ICollection<T>
     */
    function add($item) : ICollection;

    /**
     * Removes a given element from a sequence.
     * 
     * @param T $item The object to remove from the sequence.
     * 
     * @throws ItemNotFoundException if the given item could not be found in the sequence.
     * 
     * @return void
     */
    function remove(mixed $item) : void;

    /**
     * Removes a given element from a sequence at the given index.
     * 
     * @param int $index The zero-based index of the item to remove.
     * 
     * @throws NoItemFoundException if the sequence is empty.
     * @throws ItemNotFoundException if the given item could not be found in the sequence.
     * 
     * @return void
     */
    function removeAt(int $index) : void;

    /**
     * Determines whether a sequence contains a specific value.
     * 
     * @param T $item The object to locate in the sequence.
     * 
     * @return bool true if item is found in the ICollection; otherwise, false.
     */
    function contains($item) : bool;

    /**
     * Returns the element at a specified index in a sequence.
     * 
     * @param int $index The zero-based index of the item to remove.
     * 
     * @throws NoItemFoundException if the collection is empty.
     * @throws ItemNotFoundException if the given item could not be found in the sequence at the given index.
     * 
     * @return T
     */
    function getElementAt(int $index);

    /**
     * Returns the element at a specified index in a sequence or -1 if nothing was found.
     * 
     * @param T $item The object to locate in the sequence.
     * 
     * @return int The index of item if found in the list; otherwise, -1
     */
    function getIndexOf($item) : int;

    /**
     * Returns the element at a specified index in a sequence or -1 if nothing was found.
     * 
     * @param string $key The element key to search with.
     * 
     * @throws InvalidOperationException
     * 
     * @return int The index of item if found in the list; otherwise, -1
     */
    function getIndexByKey(string $key) : int;

    /**
     * Adds the elements of the specified collection to the end of the list and returns the merged list.
     * 
     * @param ICollection<T> $collection 
     * 
     * @return T[] Containing both old and new sequences of the collection.
     */
    function merge(ICollection $collection) : array;
}