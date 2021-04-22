<?php

declare(strict_types=1);

include_once realpath('.' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'bootstrap.php');

use PHPUnit\Framework\TestCase;
use Framework\Utils\Str;

/**
 * @testdox String class tests
 */
class StrTest extends TestCase {

    //============== data provider methods ================
    public function startWithDataProvider() {
        return [
            "Check if string Hello starts with letter h" => ["Hello", "h", true, true], // case insensitive
            "Check if string Hello starts with letter H" => ["Hello", "H", true, true], // case insensitive
            "Check if string Hello starts with empty string" => ["Hello", "", true, false], // case insensitive
            "Check if empty string starts with a letter" => ["", "H", true, false], // case insensitive
            "Check if string Hello starts with letter i" => ["Hello", "i", false, false] // case sensitive
        ];
    }
    public function endsWithDataProvider() {
        return [
            "Check if string Hello ends with letter h" => ["Hello", "o", true, true], // case insensitive
            "Check if string Hello ends with letter H" => ["Hello", "O", true, true], // case insensitive
            "Check if string Hello ends with empty string" => ["Hello", "", true, false],
            "Check if empty string ends with a letter" => ["", "o", true, false],
            "Check if string Hello ends with letter O" => ["Hello", "O", false, false] // case sensitive
        ];
    }
    

    //============== actual test methods =================

    /**
     * @dataProvider startWithDataProvider
     */
    public function testStringStartsWithPass(string $subject, string $needle, bool $ignoreCase, bool $expected) {
        $this->assertSame($expected, Str::startsWith($subject, $needle, $ignoreCase));
    }

    /**
     * @dataProvider endsWithDataProvider
     */
    public function testStringEndsWithPass(string $subject, string $needle, bool $ignoreCase, bool $expected) {
        $this->assertSame($expected, Str::endsWith($subject, $needle, $ignoreCase));
    }

    public function testStringEndsWithFail() {
        $this->assertFalse(Str::endsWith("Hello", "0"));    // number 0 instead of letter 0
        $this->assertFalse(Str::endsWith("", "nothing to search"));
    }

    public function testEmpty() {
        $this->assertTrue(Str::isEmpty(''));
        $this->assertTrue(Str::isEmpty(' '));
        $this->assertFalse(Str::isEmpty('a'));
    }

    /**
     * @test
     */
    public function strContains() {
        self::assertTrue(Str::contains("Hello", "ll"));
        self::assertFalse(Str::contains("Hello", "le"));
    }

    public function testLowerCase() {
        self::assertSame('hello', Str::toLower('Hello'));
        self::assertNotSame('Hello', Str::toLower('Hello'));
    }

    public function testUpperCase() {
        self::assertSame('HELLO', Str::toUpper('hello'));
        self::assertNotSame('hello', Str::toUpper('Hello'));
    }

    public function testEmail() {
        self::assertTrue(Str::isValidEmail("akeem@aweklin.com"));
        self::assertTrue(Str::isValidEmail("AKEEM@aweklin.com"));
        self::assertTrue(Str::isValidEmail("AKEEM@AWEKLIN.COM"));
        self::assertFalse(Str::isValidEmail(""));
        self::assertFalse(Str::isValidEmail("akeem@aweklincom"));
        self::assertFalse(Str::isValidEmail("akeemaweklin.com"));
    }

    public function testRemoveSpace() {
        self::assertSame('', Str::removeSpaces(null));
        self::assertSame('', Str::removeSpaces(''));
        self::assertSame('hello', Str::removeSpaces(' hello'));
        self::assertSame('hello', Str::removeSpaces('hello '));
        self::assertSame('hello', Str::removeSpaces(' hello '));
        self::assertSame('hello', Str::removeSpaces(' hel lo '));
    }

}