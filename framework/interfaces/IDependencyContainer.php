<?php

namespace Framework\Interfaces;

/**
 * Describes the interface of a container that exposes methods to read its entries.
 */
interface IDependencyContainer {
    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws IDependencyNotFoundException  No entry was found for **this** identifier.
     * @throws IDependencyContainerException Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    function get(string $id);

    /**
     * Registers a new entry to the container with the given identifier
     * 
     * @param string $id Identifier of the entry to add.
     */
    function register(string $id, callable|string $concrete) : IDependencyContainer;

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundException`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    function has(string $id): bool;
}