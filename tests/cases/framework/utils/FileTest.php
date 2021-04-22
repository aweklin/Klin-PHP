<?php

declare(strict_types=1);

include_once realpath('.' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'bootstrap.php');

use PHPUnit\Framework\TestCase;
use Framework\Utils\File;

class FileTest extends TestCase {

    public function testFileExtension() {
        self::assertEquals('png', File::getExtension('abcd.png'));
        self::assertEquals('', File::getExtension(' '));
        self::assertNotEquals('png', File::getExtension('abcd'));
    }

    public function testWrite() {
        
    }

}