<?php

namespace Framework\Exceptions;

use IDependencyNotFoundException;

/**
 * Represents a no entry was found in the dependency injection container.
 */
class DependencyNotFoundException extends ExceptionBase implements IDependencyNotFoundException {}