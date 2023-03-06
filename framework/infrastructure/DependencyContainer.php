<?php

namespace Framework\Infrastructure;

use Framework\Exceptions\DependencyContainerException;
use Framework\Exceptions\DependencyNotFoundException;
use Framework\Interfaces\IDependencyContainer;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;

class DependencyContainer implements IDependencyContainer {

    private array $_entries = [];
    
    public function get(string $id) {
        if ($this->has($id)) {
            $entry = $this->_entries[$id];

            if (is_callable($entry))
                return $entry($this);

            $id = $entry;
        }
        
        // auto-register the given dependency
        return $this->_resolve($id);
    }

    public function register(string $id, callable|string $concrete) : IDependencyContainer {
        $this->_entries[$id] = $concrete;

        return $this;
    }
    
    public function has(string $id): bool {
        return isset($this->_entries[$id]);
    }

    private function _resolve(string $id) {
        try {
            $reflectionClass = new ReflectionClass($id);
        } catch (ReflectionException $exception) {
            throw new DependencyNotFoundException("Class {$id} does not exists.", $exception->getCode(), $exception);
        }
        
        if (!$reflectionClass->isInstantiable())
            throw new DependencyContainerException("Class {$id} is not instantiable.");

        $constructor = $reflectionClass->getConstructor();
        if (!$constructor)
            return new $id;

        $parameters = $constructor->getParameters();
        if (!$parameters)
            return new $id;

        $dependencies = array_map(
            function(ReflectionParameter $parameter) use ($id) {
                $name = $parameter->getName();
                $type = $parameter->getType();

                if (!$type)
                    throw new DependencyContainerException(
                        "Failed to resolve class '{$id}' because parameter '{$name}' is missing a type hint.");

                if ($type instanceof ReflectionUnionType)
                    throw new DependencyContainerException(
                        "Failed to resolve class '{$id}' because of union type for parameter '{$parameter}'.");

                if ($type instanceof ReflectionNamedType && !$type->isBuiltin())
                    return $this->get($type->getName());
                
                return $name;
            },
            $parameters
        );

        return $reflectionClass->newInstanceArgs($dependencies);
    }
}