<?php

namespace Framework\Enums;

enum RequestType : string {
    case get = 'get';
    case post = 'post';
    case put = 'put';
    case delete = 'delete';
}