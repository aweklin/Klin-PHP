<?php

namespace Framework\Interfaces;

use IteratorAggregate;

/**
 * 
 * Represents a collection of objects that can be individually accessed by index.
 */
interface ICollection extends IteratorAggregate {

    /** 
     * Returns a list that contains elements from the input sequence.
     */
    function toList() : array;

    /**
     * Returns a number that represents how many elements in the specified sequence.
     */
    function count() : int;

    /**
     * Returns the first element of a sequence or null if the list is empty.
     */
    function first() : mixed;

    /**
     * Returns the last element of a sequence or null if the list is empty.
     */
    function last() : mixed;

    /**
     * Adds an item to a sequence.
     * 
     * @throws InvalidOperationException if the item can not be added due to it having a type different from the existing type.
     * 
     * @return ICollection
     */
    function add(mixed $item) : ICollection;

    /**
     * Removes a given element from a sequence.
     * 
     * @param mixed $item The object to remove from the sequence.
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
     * @param mixed $item The object to locate in the sequence.
     * 
     * @return bool true if item is found in the ICollection; otherwise, false.
     */
    function contains(mixed $item) : bool;

    /**
     * Returns the element at a specified index in a sequence.
     * 
     * @param int $index The zero-based index of the item to remove.
     * 
     * @throws NoItemFoundException if the collection is empty.
     * @throws ItemNotFoundException if the given item could not be found in the sequence at the given index.
     * 
     * @return mixed
     */
    function getElementAt(int $index) : mixed;

    /**
     * Returns the element at a specified index in a sequence or -1 if nothing was found.
     * 
     * @param mixed $item The object to locate in the sequence.
     * 
     * @return int The index of item if found in the list; otherwise, -1
     */
    function getIndexOf(mixed $item) : int;

    /**
     * Returns the element at a specified index in a sequence or -1 if nothing was found.
     * 
     * @param mixed $item The object to locate in the sequence.
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
     * @param ICollection $collection 
     * 
     * @return array Containing both old and new sequences of the collection.
     */
    function merge(ICollection $collection) : array;
}