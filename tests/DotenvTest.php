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
        $content = "KEY1=VALUE # this is a comment\nKEY2=VALUE#this is also a comment\n";
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

    public function testDoubleQuotedHashtagsAndSlashes()
    {
        $expected = [
            "URL" => "https://example.com/docs/#getting-started",
            "PATH" => "/var/www/example/#current",
        ];
        $content = file_get_contents(__DIR__ . "/data/double_quoted_hashtags_and_slashes.env");
        $actual = Dotenv::parse($content);
        $this->assertSame($expected, $actual);
    }

    public function testSingleQuotedHashtagsAndSlashes()
    {
        $expected = [
            "URL" => "https://example.com/docs/#getting-started",
            "PATH" => "/var/www/example/#current",
        ];
        $content = file_get_contents(__DIR__ . "/data/single_quoted_hashtags_and_slashes.env");
        $actual = Dotenv::parse($content);
        $this->assertSame($expected, $actual);
    }

    public function testDoubleQuotedBackslashes()
    {
        $expected = [
            "URL" => "https://example.com/path\\with\\backslashes",
            "PATH" => "C:\\Windows\\System32\\drivers\\etc",
        ];
        $content = file_get_contents(__DIR__ . "/data/double_quoted_backslashes.env");
        $actual = Dotenv::parse($content);
        $this->assertSame($expected, $actual);
    }

    public function testSingleQuotedBackslashes()
    {
        $expected = [
            "URL" => "https://example.com/path\\with\\backslashes",
            "PATH" => "C:\\Windows\\System32\\drivers\\etc",
        ];
        $content = file_get_contents(__DIR__ . "/data/single_quoted_backslashes.env");
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
        $content = "KJSD%ASD=\"value\"\n";
        $this->expectException(ParseException::class);
        $actual = Dotenv::parse($content);
        $this->assertSame($expected, $actual);
    }

    public function testKeyStartsWithNumber()
    {
        $expected = [];
        $content = "0KEY=VALUE\n";
        $this->expectException(ParseException::class);
        $actual = Dotenv::parse($content);
        $this->assertSame($expected, $actual);
    }

    public function testMissingKey()
    {
        $expected = [];
        $content = "=VALUE\n";
        $this->expectException(ParseException::class);
        $actual = Dotenv::parse($content);
        $this->assertSame($expected, $actual);
    }

    public function testMissingValue()
    {
        $expected = [
            'KEY' => ""
        ];
        $content = "KEY=\n";
        $actual = Dotenv::parse($content);
        $this->assertSame($expected, $actual);
    }

    public function testSimple()
    {
        $expected = [
            "TEST" => "Hello World"
        ];
        $content = "TEST=\"Hello World\"\n# comment\n";
        $actual = Dotenv::parse($content);
        $this->assertSame($expected, $actual);
    }

    public function testDuplicateKeysFromFile()
    {
        $expected = [
            "KEY1" => "value2",
        ];
        $content = "KEY1=value1\nKEY1=value2\n";
        $actual = Dotenv::parse($content);
        $this->assertSame($expected, $actual);
    }

    public function testHashesInUnquotedValuesFromFile()
    {
        $expected = [
            "URL" => "https://example.com/docs/",
            "PATH" => "/var/www/example/",
        ];
        $content = file_get_contents(__DIR__ . "/data/hashes_in_unquoted_values.env");
        $actual = Dotenv::parse($content);
        $this->assertSame($expected, $actual);
    }

    public function testMalformedLineWithoutEqualsFromFile()
    {
        $content = "ONLY_KEY\n";
        $this->expectException(ParseException::class);
        Dotenv::parse($content);
    }

    public function testNumericConversionEdgeCasesFromFile()
    {
        $expected = [
            "NEGATIVE" => -42,
            "FLOAT" => 1.5,
            "SCIENTIFIC" => 1000.0,
            "POSITIVE" => 7,
            "HEX" => 255,
            "BINARY" => 10,
            "OCTAL" => 15,
        ];
        $content = file_get_contents(__DIR__ . "/data/numeric_conversion_edge_cases.env");
        $actual = Dotenv::parse($content, true);
        $this->assertSame($expected, $actual);
    }

    public function testWhitespaceAndBom()
    {
        $expected = [
            "KEY1" => "value",
            "KEY2" => "value2",
            "KEY3" => "",
        ];
        $content = "\xEF\xBB\xBF  KEY1 = value  \r\n\tKEY2=value2\r\nKEY3=\r\n";
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
        $content = "KEY=\"sdlfknsdlkf\n";
        $this->expectException(ParseException::class);
        $actual = Dotenv::parse($content);
        $this->assertSame($expected, $actual);
    }

    public function testValueMissingSingleQuote()
    {
        $expected = [];
        $content = "KEY='aslkjhdalksd\n";
        $this->expectException(ParseException::class);
        $actual = Dotenv::parse($content);
        $this->assertSame($expected, $actual);
    }

    public function testValuesContainingEqualsSigns()
    {
        $expected = [
            "UNQUOTED" => "a=b",
            "QUOTED" => "https://example.com?a=1&b=2",
        ];
        $content = "UNQUOTED=a=b\nQUOTED=\"https://example.com?a=1&b=2\"\n";
        $actual = Dotenv::parse($content);

        $this->assertSame($expected, $actual);
    }

    public function testQuotedValuesMayHaveTrailingComments()
    {
        $expected = [
            "DOUBLE" => "value",
            "SINGLE" => "value",
        ];
        $content = "DOUBLE=\"value\" # comment\nSINGLE='value' # comment\n";
        $actual = Dotenv::parse($content);

        $this->assertSame($expected, $actual);
    }

    public function testQuotedValuesPreserveEscapedQuotesAndBackslashes()
    {
        $content = 'DOUBLE="value\\"inside"' . "\nSINGLE='value\\'inside'\n";

        $this->assertSame([
            "DOUBLE" => 'value\\"inside',
            "SINGLE" => "value\\'inside",
        ], Dotenv::parse($content));
    }

    public function testUnquotedHashesStartComments()
    {
        $expected = [
            "EMBEDDED" => "abc",
            "ONLY" => "",
            "SPACED" => "value",
        ];
        $content = "EMBEDDED=abc#def\nONLY=#value\nSPACED=value # comment\n";
        $actual = Dotenv::parse($content);

        $this->assertSame($expected, $actual);
    }

    public function testUnderscoreKeysAreValidAndEmptyKeysAreNot()
    {
        $expected = [
            "_" => "one",
            "_KEY" => "two",
            "KEY_NAME" => "three",
        ];
        $actual = Dotenv::parse("_=one\n_KEY=two\nKEY_NAME=three\n");

        $this->assertSame($expected, $actual);
    }

    public function testParseExceptionIncludesThePhysicalLineNumber()
    {
        try {
            Dotenv::parse("FIRST=value\n\n\nBROKEN LINE\n");
            $this->fail('Expected a parse exception.');
        } catch (ParseException $exception) {
            $this->assertStringContainsString('near BROKEN LINE', $exception->getMessage());
            $this->assertStringContainsString('at line 4', $exception->getMessage());
        }
    }

    public function testMalformedLineErrorMessageIncludesTheOffendingLine()
    {
        try {
            Dotenv::parse("BROKEN LINE\n");
            $this->fail('Expected a parse exception.');
        } catch (ParseException $exception) {
            $this->assertSame(
                'Each line must be of following format: KEY=value near BROKEN LINE at line 1',
                $exception->getMessage()
            );
        }
    }

    public function testEmptyCommentsAndWhitespaceReturnNoVariables()
    {
        $expected = [];
        $actual = Dotenv::parse("\n  \n# comment\n\t# another comment\n");

        $this->assertSame($expected, $actual);
    }

    public function testMixedLineEndingsAreSupported()
    {
        $expected = [
            "ONE" => "1",
            "TWO" => "2",
            "THREE" => "3",
        ];
        $content = "ONE=1\rTWO=2\r\nTHREE=3\n";
        $actual = Dotenv::parse($content);

        $this->assertSame($expected, $actual);
    }

    public function testMalformedDuplicateKeyStillThrows()
    {
        $this->expectException(ParseException::class);
        Dotenv::parse("KEY=valid\nKEY=\"unterminated\n");
    }

    public function testQuotedValuesAreConverted()
    {
        $expected = [
            "BOOLEAN" => true,
            "NULL" => null,
            "NUMBER" => 42,
        ];
        $content = "BOOLEAN=\"true\"\nNULL=\"null\"\nNUMBER=\"42\"\n";
        $actual = Dotenv::parse($content, true);

        $this->assertSame($expected, $actual);
    }

    public function testNumericConversionBoundaries()
    {
        $expected = [
            "LEADING_ZERO" => 8,
            "UPPER_HEX" => 255,
            "UPPER_BINARY" => 10,
            "UPPER_OCTAL" => 15,
            "INVALID_HEX" => "0x",
            "INVALID_BINARY" => "0b2",
            "INVALID_OCTAL" => "0o8",
        ];
        $content = "LEADING_ZERO=08\nUPPER_HEX=0XFF\nUPPER_BINARY=0B1010\nUPPER_OCTAL=0O17\nINVALID_HEX=0x\nINVALID_BINARY=0b2\nINVALID_OCTAL=0o8\n";
        $actual = Dotenv::parse($content, true);

        $this->assertSame($expected, $actual);
    }

    public function testLargeAndOverflowNumbersAreConverted()
    {
        $actual = Dotenv::parse("LARGE=999999999999999999999\nOVERFLOW=1e999\n", true);

        $this->assertSame(PHP_INT_MAX, $actual["LARGE"]);
        $this->assertSame(INF, $actual["OVERFLOW"]);
    }
}
