<?php

namespace Framework\Enums;

enum LogLevel : string {
    case INFO = 'INF';
    case WARNING = 'WRN';
    case ERROR = 'ERR';
    case DEBUG = 'DEB';
}