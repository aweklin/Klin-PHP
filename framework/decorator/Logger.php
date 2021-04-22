<?php

namespace Framework\Decorator;

use Framework\Interfaces\ILogger;

abstract class Logger implements ILogger {   
    
    abstract public function log($text);

}