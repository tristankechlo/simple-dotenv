<?php

use TK\Dotenv\ParseException;
use TK\Dotenv\Dotenv;

class DotenvTest extends \PHPUnit\Framework\TestCase
{

    public function testCommentsInValue()
    {
        $expected = [
            "KEY1" => "VALUE",
            "KEY2" => "VALUE"
        ];
        $content = file_get_contents(__DIR__ . "/data/comments_in_value.env");
        $actual = Dotenv::parse($content);
        $this->assertSame($expected, $actual);
    }

    public function testConvertBooleanOn()
    {
        $expected = [
            "KEY1" => true,
            "KEY2" => false,
            "KEY3" => true,
            "KEY4" => false,
            "KEY5" => true,
            "KEY6" => false,
            "KEY7" => true,
            "KEY8" => false,
            "KEY9" => true,
            "KEY10" => false,
        ];
        $content = file_get_contents(__DIR__ . "/data/convert_boolean.env");
        $actual = Dotenv::parse($content, true);
        $this->assertSame($expected, $actual);
    }

    public function testConvertBooleanOff()
    {
        $expected = [
            "KEY1" => "true",
            "KEY2" => "false",
            "KEY3" => "yes",
            "KEY4" => "no",
            "KEY5" => "True",
            "KEY6" => "False",
            "KEY7" => "YES",
            "KEY8" => "NO",
            "KEY9" => "on",
            "KEY10" => "OFF",
        ];
        $content = file_get_contents(__DIR__ . "/data/convert_boolean.env");
        $actual = Dotenv::parse($content);
        $this->assertSame($expected, $actual);
    }

    public function testConvertNullOn()
    {
        $expected = [
            "KEY1" => "",
            "KEY2" => null,
            "KEY3" => null,
        ];
        $content = file_get_contents(__DIR__ . "/data/convert_null.env");
        $actual = Dotenv::parse($content, true);
        $this->assertSame($expected, $actual);
    }

    public function testConvertNullOff()
    {
        $expected = [
            "KEY1" => "",
            "KEY2" => "null",
            "KEY3" => "NONE",
        ];
        $content = file_get_contents(__DIR__ . "/data/convert_null.env");
        $actual = Dotenv::parse($content);
        $this->assertSame($expected, $actual);
    }

    public function testConvertNumberOn()
    {
        $expected = [
            "KEY1" => 1,
            "KEY2" => 1.1,
            "KEY3" => "33 33",
            "KEY4" => 160,
            "KEY5" => 51,
            "KEY6" => 63,
        ];
        $content = file_get_contents(__DIR__ . "/data/convert_numbers.env");
        $actual = Dotenv::parse($content, true);
        $this->assertSame($expected, $actual);
    }

    public function testConvertNumberOff()
    {
        $expected = [
            "KEY1" => "1",
            "KEY2" => "1.1",
            "KEY3" => "33 33",
            "KEY4" => "0xa0",
            "KEY5" => "0b110011",
            "KEY6" => "0o77",
        ];
        $content = file_get_contents(__DIR__ . "/data/convert_numbers.env");
        $actual = Dotenv::parse($content);
        $this->assertSame($expected, $actual);
    }

    public function testEmptyFile()
    {
        $expected = [];
        $content = file_get_contents(__DIR__ . "/data/empty_file.env");
        $actual = Dotenv::parse($content);
        $this->assertSame($expected, $actual);
    }

    public function testKeyInvalidLetters()
    {
        $expected = [];
        $content = file_get_contents(__DIR__ . "/data/key_invalid_letters.env");
        $this->expectException(ParseException::class);
        $actual = Dotenv::parse($content);
        $this->assertSame($expected, $actual);
    }

    public function testKeyStartsWithNumber()
    {
        $expected = [];
        $content = file_get_contents(__DIR__ . "/data/key_starts_with_number.env");
        $this->expectException(ParseException::class);
        $actual = Dotenv::parse($content);
        $this->assertSame($expected, $actual);
    }

    public function testMissingKey()
    {
        $expected = [];
        $content = file_get_contents(__DIR__ . "/data/missing_key.env");
        $this->expectException(ParseException::class);
        $actual = Dotenv::parse($content);
        $this->assertSame($expected, $actual);
    }

    public function testMissingValue()
    {
        $expected = [
            'KEY' => ""
        ];
        $content = file_get_contents(__DIR__ . "/data/missing_value.env");
        $actual = Dotenv::parse($content);
        $this->assertSame($expected, $actual);
    }

    public function testSimple()
    {
        $expected = [
            "TEST" => "Hello World"
        ];
        $content = file_get_contents(__DIR__ . "/data/simple.env");
        $actual = Dotenv::parse($content);
        $this->assertSame($expected, $actual);
    }

    public function testSpaces()
    {
        $expected = [
            "KEY1" => "VALUE",
            "KEY2" => "VALUE",
            "KEY3" => "VALUE",
            "KEY4" => "VALUE",
            "KEY5" => "VALUE",
            "KEY6" => "VALUE",
            "KEY7" => "VALUE",
        ];
        $content = file_get_contents(__DIR__ . "/data/spaces.env");
        $actual = Dotenv::parse($content);
        $this->assertSame($expected, $actual);
    }

    public function testValueMissingDoubleQuote()
    {
        $expected = [];
        $content = file_get_contents(__DIR__ . "/data/value_missing_double_quote.env");
        $this->expectException(ParseException::class);
        $actual = Dotenv::parse($content);
        $this->assertSame($expected, $actual);
    }

    public function testValueMissingSingleQuote()
    {
        $expected = [];
        $content = file_get_contents(__DIR__ . "/data/value_missing_single_quote.env");
        $this->expectException(ParseException::class);
        $actual = Dotenv::parse($content);
        $this->assertSame($expected, $actual);
    }
}
